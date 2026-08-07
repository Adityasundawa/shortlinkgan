<?php

namespace App\Http\Controllers;

use App\Models\DomainDecentralize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;

class DomainDecentralizeController extends Controller
{
    public function index(){
        $domains = DomainDecentralize::orderBy('domain_url')->get();
        return view('admin.domain_decentralized.list', compact(['domains']));
    }


    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'domain' => 'required',
        ]);
        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator)->withInput();
        }

        $newDomain = new DomainDecentralize();
        $newDomain->domain_url = $request->domain;
        $newDomain->status = $request->status ? 'enable' : 'disable';
        $newDomain->api_key = Str::random(12);
        $newDomain->save();
        Alert::success('Sukses', 'Domain '.$request->domain.' Berhasil di tambahkan');
        return redirect()->route('admin.domain_decentralized')->with(['message' => 'saved']);
    }


    public function update(Request $request){
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'domain' => 'required',
        ]);
        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator)->withInput();
        }

        DomainDecentralize::where('id', $request->id)
        ->update([
            'domain_url' => $request->domain,
            'status' => $request->status ? 'enable' : 'disable'
        ]);
        Alert::success('Sukses', 'Domain '.$request->domain.' Berhasil di update');
        return redirect()->route('admin.domain_decentralized')->with(['message' => 'updated']);
    }


    public function switchStatus(Request $request){
        $validator = Validator::make($request->all(), [
            'dataID' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message'=>$validator->errors()], 500);
        }

        $data = DomainDecentralize::where('id', $request->dataID)->firstOrFail();
        $setStatus = $data->status == "enable" ? "disable" : "enable";

        DomainDecentralize::where('id', $request->dataID)->update(['status' => $setStatus]);
        return response()->json(["status" => "success", "message"=>"data has been updated", "set_status"=>$setStatus], 200);
    }


    public function detail($id){
        $data = DomainDecentralize::where('id', $id)->firstOrFail();
        return response()->json([
            'domain' => $data->domain_url,
            'status' => $data->status
        ], 200);
    }


    public function destroy(Request $request){
        $validator = Validator::make($request->all(), [
            'dataID' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message'=>$validator->errors()], 500);
        }

        DomainDecentralize::where('id', $request->dataID)->delete();
        return response()->json(["status" => "success", "message"=>"data has been deleted"], 200);
    }
}
