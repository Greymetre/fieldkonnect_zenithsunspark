<?php

namespace App\Http\Controllers;

use App\Models\CustomerType;
use App\Models\LoyaltyAppSetting;
use App\Models\Media;
use Illuminate\Http\Request;

class LoyaltyAppSettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->loyalty_app_setting = new LoyaltyAppSetting();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->loyalty_app_setting = LoyaltyAppSetting::first();
        $customer_types = CustomerType::where('active', 'Y')->get();
        return view('loyalty_app_setting.index', compact('customer_types'))->with('loyalty_app_setting', $this->loyalty_app_setting);
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
        if ($request->id && $request->id != '' && $request->id != NULL) {
            $loyaltyAppSetting = LoyaltyAppSetting::find($request->id);
            $loyaltyAppSetting->customer_types = implode(',', $request->customer_types);
            $loyaltyAppSetting->app_version = $request->app_version;
            $loyaltyAppSetting->save();
        } else {
            $loyaltyAppSetting = new LoyaltyAppSetting();
            $loyaltyAppSetting->customer_types = implode(',', $request->customer_types);
            $loyaltyAppSetting->app_version = $request->app_version;
            $loyaltyAppSetting->save();
        }
        if ($request->hasFile('images')) {
            $files = $request->file('images');
            foreach ($files as $file) {
                $customname = time() . '.' . $file->getClientOriginalExtension();
                $loyaltyAppSetting->addMedia($file)
                    ->usingFileName($customname)
                    ->toMediaCollection('slider_image');
            }
        }
        if ($request->hasFile('gift_images')) {
            $files = $request->file('gift_images');
            foreach ($files as $file) {
                $customname = time() . '.' . $file->getClientOriginalExtension();
                $loyaltyAppSetting->addMedia($file)
                    ->usingFileName($customname)
                    ->toMediaCollection('gift_slider_image');
            }
        }

        if ($request->hasFile('loyalty_side_menu_image')) {
            $loyaltyAppSetting->clearMediaCollection('loyalty_side_menu_image');
            $file = $request->file('loyalty_side_menu_image'); // No need for [0]
            $customName = time() . '.' . $file->getClientOriginalExtension();

            $loyaltyAppSetting->addMedia($file)
                ->usingFileName($customName)
                ->toMediaCollection('loyalty_side_menu_image'); // Replaces old image automatically
        }else {
            // If no image is provided in the request, remove existing image if exists
            if ($loyaltyAppSetting->hasMedia('loyalty_side_menu_image')) {
                $loyaltyAppSetting->clearMediaCollection('loyalty_side_menu_image');
            }
        }

        if ($request->hasFile('product_catalogue')) {
            $file = $request->file('product_catalogue');
            $customname = time() . '.' . $file->getClientOriginalExtension();
            $loyaltyAppSetting->addMedia($file)
                ->usingFileName($customname)
                ->toMediaCollection('product_catalogue');
        }

        if ($request->hasFile('scheme_catalogue')) {
            $file = $request->file('scheme_catalogue');
            $customname = time() . '.' . $file->getClientOriginalExtension();
            $loyaltyAppSetting->addMedia($file)
                ->usingFileName($customname)
                ->toMediaCollection('scheme_catalogue');
        }

        if ($request->hasFile('terms_condition')) {
            $file = $request->file('terms_condition');
            $customname = time() . '.' . $file->getClientOriginalExtension();
            $loyaltyAppSetting->addMedia($file)
                ->usingFileName($customname)
                ->toMediaCollection('terms_condition');
        }

        return redirect('loyalty-app-setting')->with('success', 'Loyalty App setting save successfully !!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\LoyaltyAppSetting  $loyaltyAppSetting
     * @return \Illuminate\Http\Response
     */
    public function show(LoyaltyAppSetting $loyaltyAppSetting)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\LoyaltyAppSetting  $loyaltyAppSetting
     * @return \Illuminate\Http\Response
     */
    public function edit(LoyaltyAppSetting $loyaltyAppSetting)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\LoyaltyAppSetting  $loyaltyAppSetting
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, LoyaltyAppSetting $loyaltyAppSetting)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\LoyaltyAppSetting  $loyaltyAppSetting
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $loyaltyAppSettingImage = Media::find($id);
        if ($loyaltyAppSettingImage) {
            $loyaltyAppSettingImage->delete();
            return response()->json(['status' => 'success', 'message' => 'Slider image delete successfully !!']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Slider image not found !!']);
        }
    }
}
