<?php

namespace App\Http\Controllers;

use App\Models\Division;
use Illuminate\Http\Request;
use App\Models\FieldKonnectAppSetting;
use App\Models\Media;
use Validator;

class FieldKonnectAppSettings extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware('auth');
        $this->field_konnect_app_setting = new FieldKonnectAppSetting();
    }

    public function index()
    {
        $this->field_konnect_app_setting =  FieldKonnectAppSetting::first();
        $divisions = Division::all();
        return view('field_connect_app_setting.index', compact('divisions'))->with('field_konnect_app_setting', $this->field_konnect_app_setting);

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
            'app_version' => 'required',
        ]);
    
        // Check if validation fails
        if ($validator->fails()) {
            // Redirect back with input and error messages
            return redirect()->back()
                             ->withErrors($validator)
                             ->withInput();
        }
        $field_konnect_app_setting =  FieldKonnectAppSetting::first();
        $data = $request->all();
        if(isset($field_konnect_app_setting)){
            if ($request->hasFile('product_catalogue')) {
                $file = $request->file('product_catalogue');
                $customname = time() . '.' . $file->getClientOriginalExtension();
                $field_konnect_app_setting->addMedia($file)
                    ->usingFileName($customname)
                    ->toMediaCollection('product_catalogue');
            }
            $field_konnect_app_setting->update($data);
            return redirect('field-konnect-app-setting')->with('success', 'Field Konnect App setting save successfully !!');
        }
        return redirect('field-konnect-app-setting')->with('error', 'Something Went Wrong Please Try !!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $field_konnect_app_setting_media = Media::find($id);
        if ($field_konnect_app_setting_media) {
            $field_konnect_app_setting_media->delete();
            return response()->json(['status' => 'success', 'message' => 'Slider image delete successfully !!']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Slider image not found !!']);
        }
    }

 
}
