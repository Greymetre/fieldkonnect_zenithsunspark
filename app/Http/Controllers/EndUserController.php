<?php

namespace App\Http\Controllers;

use App\DataTables\EndUserDataTable;
use App\Exports\EndUserExport;
use App\Models\City;
use App\Models\District;
use App\Models\EndUser;
use App\Models\Pincode;
use App\Models\State;
use Illuminate\Http\Request;
use Validator;
use Gate;
use Excel;
use Illuminate\Http\Response;

class EndUserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(EndUserDataTable $dataTable, Request $request)
    {
        abort_if(Gate::denies('end_user_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return $dataTable->render('end_user.index');
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $states = State::where('active', 'Y')->get();
        $pincodes = Pincode::where('active', 'Y')->get();
        $endUser = new EndUser();
        return view('end_user.form', compact('endUser', 'states', 'pincodes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'customer_number'    => 'required',
            'customer_state'          => 'required',
            'customer_district'    => 'required',
            'customer_city'            => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->passes()) {
            $Cstate = State::where('id', $request->customer_state)->first();
            $Cdistrict = District::where('id', $request->customer_district)->first();
            $Ccity = City::where('id', $request->customer_city)->first();

            $end_user = EndUser::updateOrCreate(['customer_number' => $request->customer_number ?? ''], [
                'customer_name' => $request->customer_name ?? '',
                'customer_number' => $request->customer_number ?? '',
                'customer_email' => $request->customer_email ?? '',
                'customer_address' => $request->customer_address ?? '',
                'customer_place' => $request->customer_place ?? '',
                'customer_pindcode' => $request->customer_pindcode ?? '',
                'customer_country' => $request->customer_country ?? '',
                'customer_state' => $Cstate->state_name ?? '',
                'customer_district' => $Cdistrict->district_name ?? '',
                'customer_city' => $Ccity->city_name ?? '',
                'state_id' => $Cstate->id ?? '',
                'district_id' => $Cdistrict->id ?? '',
                'city_id' => $Ccity->id ?? '',
                'status' => $request->customer_status??'1',
            ]);
            return redirect()->route('end_user.index')->with('message_success', 'End User updated successfully');
        } else {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\EndUser  $endUser
     * @return \Illuminate\Http\Response
     */
    public function show(EndUser $endUser)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\EndUser  $endUser
     * @return \Illuminate\Http\Response
     */
    public function edit(EndUser $endUser)
    {
        $states = State::where('active', 'Y')->get();
        $pincodes = Pincode::where('active', 'Y')->get();
        return view('end_user.form', compact('endUser', 'states', 'pincodes'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\EndUser  $endUser
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EndUser $endUser)
    {
        $rules = [
            'customer_number'    => 'required',
            'customer_state'          => 'required',
            'customer_district'    => 'required',
            'customer_city'            => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->passes()) {
            $Cstate = State::where('id', $request->customer_state)->first();
            $Cdistrict = District::where('id', $request->customer_district)->first();
            $Ccity = City::where('id', $request->customer_city)->first();
            
            $end_user = EndUser::updateOrCreate(['customer_number' => $request->customer_number ?? ''], [
                'customer_name' => $request->customer_name ?? '',
                'customer_number' => $request->customer_number ?? '',
                'customer_email' => $request->customer_email ?? '',
                'customer_address' => $request->customer_address ?? '',
                'customer_place' => $request->customer_place ?? '',
                'customer_pindcode' => $request->customer_pindcode ?? '',
                'customer_country' => $request->customer_country ?? '',
                'customer_state' => $Cstate->state_name ?? '',
                'customer_district' => $Cdistrict->district_name ?? '',
                'customer_city' => $Ccity->city_name ?? '',
                'state_id' => $Cstate->id ?? '',
                'district_id' => $Cdistrict->id ?? '',
                'city_id' => $Ccity->id ?? '',
                'status' => $request->customer_status,
            ]);
            return redirect()->route('end_user.index')->with('message_success', 'End User updated successfully');
        } else {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\EndUser  $endUser
     * @return \Illuminate\Http\Response
     */
    public function destroy(EndUser $endUser)
    {
        //
    }

    public function active(Request $request)
    {
        if (EndUser::where('id', $request['id'])->update(['status' => ($request['active'] == '1') ? '0' : '1'])) {
            $user = EndUser::find($request['id']);
            
            $message = ($request['active'] == '1') ? 'Inactive' : 'Active';
            return response()->json(['status' => 'success', 'message' => 'End User ' . $message . ' Successfully!']);
        }
        return response()->json(['status' => 'error', 'message' => 'Error in Status Update']);
    }

    public function download(Request $request)
    {
        abort_if(Gate::denies('end_user_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        // return $request;
        return Excel::download(new EndUserExport($request), 'EndUser.xlsx');
    }
}
