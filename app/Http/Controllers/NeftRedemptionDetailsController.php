<?php

namespace App\Http\Controllers;

use App\Models\NeftRedemptionDetails;
use App\Models\Redemption;
use Illuminate\Http\Request;
use App\Http\Controllers\SendNotifications;
use App\Models\Customers;

class NeftRedemptionDetailsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\NeftRedemptionDetails  $neftRedemptionDetails
     * @return \Illuminate\Http\Response
     */
    public function show(NeftRedemptionDetails $neftRedemptionDetails)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\NeftRedemptionDetails  $neftRedemptionDetails
     * @return \Illuminate\Http\Response
     */
    public function edit(NeftRedemptionDetails $neftRedemptionDetails)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\NeftRedemptionDetails  $neftRedemptionDetails
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, NeftRedemptionDetails $neftRedemptionDetails)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\NeftRedemptionDetails  $neftRedemptionDetails
     * @return \Illuminate\Http\Response
     */
    public function destroy(NeftRedemptionDetails $neftRedemptionDetails)
    {
        //
    }

    public function changeStatus(Request $request)
    {
        if($request->status == '3'){
            if($request->tds == '' || $request->tds == null){
                $request->tds = 10;
            }
        }
        NeftRedemptionDetails::updateOrCreate([
            'redemption_id' => $request->id,],[
            'utr_number' => $request->utr_number,
            'tds' => $request->tds,
            'remark' => $request->remark
        ]);

        $updateStatus = Redemption::where('id', $request->id)->update(['status' => $request->status]);
        if($updateStatus){
            $redemption = Redemption::find($request->id);
            if ($redemption) {
                if ($redemption->redeem_mode == '2') {
                    if ($request->status == '1') {
                        $status = 'Approved';
                    } elseif ($request->status == '2') {
                        $status = 'Rejected';
                    } elseif ($request->status == '3') {
                        $status = 'Success';
                    } elseif ($request->status == '4') {
                        $status = 'Fail';
                    } elseif ($request->status == '0') {
                        $status = 'Pendding';
                    }
                } elseif ($redemption->redeem_mode == '1') {
                    if ($request->status == '1') {
                        $status = 'Approved';
                    } elseif ($request->status == '2') {
                        $status = 'Rejected';
                    } elseif ($request->status == '3') {
                        $status = 'Dispatch';
                    } elseif ($request->status == '4') {
                        $status = 'Success';
                    } elseif ($request->status == '0') {
                        $status = 'Pendding';
                    }
                }
            }
            $customer = Customers::with('customerdetails')->find($redemption->customer_id);
            $noti_data = [
                'fcm_token' =>  $customer->customerdetails->fcm_token,
                'title' => 'Redemption is ' . $status . ' ✅',
                'msg' => $customer->first_name . ', your redemption is ' . $status,
            ];
            $send_notification = SendNotifications::send($noti_data);
            return response()->json(['status' => 'success','message' => 'Redemption status change successfully!']);
        }else{
            return response()->json(['status' => 'error','message' => 'Error in change status of redemption!']);
        }
    }
}
