<?php

namespace App\Http\Controllers;

use App\Models\PowerBiSetting;
use Illuminate\Http\Request;

class PowerBiSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $all_settings = PowerBiSetting::all()->pluck('value', 'key')->toArray();
        return view('powerbi.index', compact('all_settings'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->except('_token');
        foreach ($data as $key => $value) {
            PowerBiSetting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return redirect()->back()->with('message_success', 'Settings Updated Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PowerBiSetting  $powerBiSetting
     * @return \Illuminate\Http\Response
     */
    public function show(PowerBiSetting $powerBiSetting)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PowerBiSetting  $powerBiSetting
     * @return \Illuminate\Http\Response
     */
    public function edit(PowerBiSetting $powerBiSetting)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PowerBiSetting  $powerBiSetting
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PowerBiSetting $powerBiSetting)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PowerBiSetting  $powerBiSetting
     * @return \Illuminate\Http\Response
     */
    public function destroy(PowerBiSetting $powerBiSetting)
    {
        //
    }
}
