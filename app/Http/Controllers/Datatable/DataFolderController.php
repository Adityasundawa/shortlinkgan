<?php

namespace App\Http\Controllers\Datatable;

use App\Http\Controllers\Controller;
use App\Models\Folder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DataFolderController extends Controller
{
    public function data(Request $request){        
        $data = Folder::with(['author'])->latest()->get();
        return DataTables::of($data)
        ->addIndexColumn()
        ->addColumn('parent', function($row){
            if( $row->id_folder_parent == 0 ){
                return '-- Parent --';
            }else{
                return $row->parentFolder->name;
            }
        })
        ->addColumn('date', function($row){
            return '<span data=sort="'.Carbon::parse($row->created_at)->format('Y-m-d').'">'.Carbon::parse($row->created_at)->format('d M Y').'</span>';
        })
        ->addColumn('action', function($row){
            return '
                <button type="button" class="btn btn-sm rounded-0 btn-outline-danger" data-id="'.$row->id.'" onclick="remove(this)"><i class="ti ti-trash"></i> Delete</button>
                <a href="'.route('admin.folder.edit', ['id'=>$row->id]).'" class="btn btn-sm rounded-0 btn-primary"><i class="ti ti-pencil"></i> Edit</a>
            ';
        })
        ->rawColumns(['date', 'action'])
        ->make(true);

        // if ($request->ajax()) {
        // }
    }
}
