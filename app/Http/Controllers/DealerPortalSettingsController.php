<?php

namespace App\Http\Controllers;

use App\Models\DealerPortalSettings;
use App\Models\Media;
use Illuminate\Http\Request;
use Validator;

class DealerPortalSettingsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->dealer_portal_setting = new DealerPortalSettings();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // if($request->ip() != '111.118.252.250'){
        //     return view('work_in_progress');
        // }
        $this->dealer_portal_setting =  DealerPortalSettings::first();
        return view('dealer_portal_setting.index')->with('dealer_portal_setting', $this->dealer_portal_setting);
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
        $validator = Validator::make($request->all(), [
            'slider' => 'required',
        ]);
    
        // Check if validation fails
        if ($validator->fails()) {
            // Redirect back with input and error messages
            return redirect()->back()
                             ->withErrors($validator)
                             ->withInput();
        }
        $dealer_portal_setting =  DealerPortalSettings::first();
        $data = $request->all();
        if(isset($dealer_portal_setting)){
            if ($request->hasFile('dealer_portal_slider_image')) {
                foreach($request->file('dealer_portal_slider_image') as $file){
                    // $file = $request->file('dealer_portal_slider_image');
                    $customname = time() . '.' . $file->getClientOriginalExtension();
                    $dealer_portal_setting->addMedia($file)
                        ->usingFileName($customname)
                        ->toMediaCollection('dealer_portal_slider_image');
                }
            }
            $dealer_portal_setting->update($data);
            return redirect('delar-portal-setting')->with('success', 'Field Konnect App setting save successfully !!');
        }
        return redirect('delar-portal-setting')->with('error', 'Something Went Wrong Please Try !!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DealerPortalSettings  $dealerPortalSettings
     * @return \Illuminate\Http\Response
     */
    public function show(DealerPortalSettings $dealerPortalSettings)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\DealerPortalSettings  $dealerPortalSettings
     * @return \Illuminate\Http\Response
     */
    public function edit(DealerPortalSettings $dealerPortalSettings)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\DealerPortalSettings  $dealerPortalSettings
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DealerPortalSettings $dealerPortalSettings)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DealerPortalSettings  $dealerPortalSettings
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $dealer_portal_setting_media = Media::find($id);
        if ($dealer_portal_setting_media) {
            $dealer_portal_setting_media->delete();
            return response()->json(['status' => 'success', 'message' => 'Slider image delete successfully !!']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Slider image not found !!']);
        }
    }
}
