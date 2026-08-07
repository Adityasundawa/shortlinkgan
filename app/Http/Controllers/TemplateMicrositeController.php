<?php

namespace App\Http\Controllers;

use App\Models\TemplateMicrosite;
use App\Http\Requests\StoreTemplateMicrositeRequest;
use App\Http\Requests\UpdateTemplateMicrositeRequest;
use Illuminate\Http\Request;

class TemplateMicrositeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function get_preview(Request $request)
    {

        $dataValue = $request['dataValue'];
        $templateMicrosite = TemplateMicrosite::find($dataValue);

        if ($templateMicrosite) {
            $filename = $templateMicrosite->preview;
            $previewImageURL = asset('assets/images/template/' . $filename);

            return response()->json(['result' => 'success', 'data' => ['preview' => $previewImageURL]]);
        } else {
            return response()->json(['result' => 'error', 'message' => 'TemplateMicrosite not found']);
        }
    }

    /**a
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTemplateMicrositeRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(TemplateMicrosite $templateMicrosite)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TemplateMicrosite $templateMicrosite)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTemplateMicrositeRequest $request, TemplateMicrosite $templateMicrosite)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TemplateMicrosite $templateMicrosite)
    {
        //
    }
}
