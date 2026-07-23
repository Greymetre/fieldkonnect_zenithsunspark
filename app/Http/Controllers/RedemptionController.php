<?php

namespace App\Http\Controllers;

use App\Models\Redemption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use App\DataTables\RedemptionDataTable;
use App\DataTables\GiftRedemptionDataTable;
use App\DataTables\RedemptionGiftsDataTable;
use Gate;
use Excel;
use App\Exports\RedemptionExport;
use App\Exports\RedemptionGiftTemplate;
use App\Exports\RedemptionTemplate;
use App\Imports\RedemptionImport;
use App\Models\Branch;
use App\Models\Customers;
use App\Models\Gifts;
use App\Models\SchemeHeader;
use Validator;
use App\Http\Controllers\SendNotifications;

class RedemptionController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->redemption = new Redemption();
        $this->path = 'redemption';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(RedemptionDataTable $dataTable, Request $request)
    {
        abort_if(Gate::denies('transaction_history_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $branches = Branch::where('active', 'Y')->get();
        $parent_customers = [];
        // $parent_customers = Customers::where('active', 'Y')->whereIn('customertype', ['1','3'])->select('id', 'name')->get();
        $redeem_modes = Config('constants.redeem_mode');
        return $dataTable->render('redemption.index', compact('branches', 'parent_customers', 'redeem_modes'));
    }

    public function gifttable(GiftRedemptionDataTable $dataTable, Request $request)
    {
        abort_if(Gate::denies('transaction_history_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $branches = Branch::where('active', 'Y')->get();
        $parent_customers = Customers::where('active', 'Y')->whereIn('customertype', ['1', '3'])->select('id', 'name')->get();
        $redeem_modes = Config('constants.redeem_mode');
        return $dataTable->render('redemption.index', compact('branches', 'parent_customers', 'redeem_modes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_if(Gate::denies('redemption_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $customers = [];
        $redeem_modes = Config('constants.redeem_mode');
        $gifts = Gifts::where('active', 'Y')->get();
        return view('redemption.create', compact('customers', 'gifts', 'redeem_modes'))->with('redemption', $this->redemption);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            abort_if(Gate::denies('transaction_history_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
            $rules = [
                'customer_id' => 'required',
            ];
            if ($request->redeem_mode == '2') {
                $rules['redeem_amount'] = 'required|numeric|max:' . $request->input('total_point');
            }
            if ($request->redeem_mode == '1') {
                $rules['gift_id'] = 'required';
            }
            $validator = Validator::make($request->all(), $rules);
            $validator->setCustomMessages([
                'redeem_amount.max' => 'The redeem amount must not be greater than total point.',
                'gift_id.required' => 'Please select at least one gift.',
            ]);
            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }
            if ($request->redeem_mode == '2') {
                Redemption::create([
                    'customer_id' => $request->customer_id,
                    'redeem_mode' => $request->redeem_mode,
                    'account_holder' => $request->account_holder,
                    'account_number' => $request->account_number,
                    'bank_name' => $request->bank_name,
                    'ifsc_code' => $request->ifsc_code,
                    'redeem_amount' => $request->redeem_amount,
                    'created_by' => auth()->user()->id,
                ]);
            } elseif ($request->redeem_mode == '1') {
                $tottal_redeem_point = Gifts::whereIn('id', $request->gift_id)->sum('points');
                if ($tottal_redeem_point > $request->input('total_point')) {
                    return redirect()->back()->withErrors('The redeem amount not be greater than to total point.');
                }
                foreach ($request->gift_id as $gift) {
                    $redeem_point = Gifts::where('id', $gift)->value('points');
                    Redemption::create([
                        'customer_id' => $request->customer_id,
                        'redeem_mode' => $request->redeem_mode,
                        'gift_id' => $gift,
                        'redeem_amount' => $redeem_point,
                        'created_by' => auth()->user()->id,
                    ]);
                }
            }

            return Redirect::to('redemptions')->with('message_success', 'Redemption Store Successfully');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TransactionHistory  $transactionHistory
     * @return \Illuminate\Http\Response
     */
    public function show(TransactionHistory $transactionHistory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TransactionHistory  $transactionHistory
     * @return \Illuminate\Http\Response
     */
    public function edit(Redemption $Redemption)
    {
        abort_if(Gate::denies('redemption_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $this->redemption = $Redemption;
        $customers = Customers::where('customertype', '2')->select('id', 'name', 'mobile')->get();
        $redeem_modes = Config('constants.redeem_mode');
        return view('redemption.create', compact('customers', 'redeem_modes'))->with('redemption', $this->redemption);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Redemption  $Redemption
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Redemption $Redemption)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required',
            'redeem_amount' => 'required|numeric|max:' . $request->input('total_point'),
        ]);
        $validator->setCustomMessages([
            'redeem_amount.max' => 'The redeem amount must not be greater than total point.',
        ]);
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $Redemption->update($request->all());

        return Redirect::to('redemptions')->with('message_success', 'Redemption Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Redemption  $Redemption
     * @return \Illuminate\Http\Response
     */
    public function destroy(Redemption $Redemption)
    {
        if ($Redemption->delete()) {
            return response()->json(['status' => 'success', 'message' => 'Redemption deleted successfully!']);
        }
        return response()->json(['status' => 'error', 'message' => 'Error in Redemption Delete!']);
    }

    public function download(Request $request)
    {
        abort_if(Gate::denies('redemption_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new RedemptionExport($request), 'Redemption.xlsx');
    }

    public function changeStatus(Request $request)
    {
        $redemption = Redemption::find($request->id);
        if ($redemption->redeem_mode == '1') {
            if ($request->status == '1') {
                $column = 'approve_date';
            } elseif ($request->status == '3') {
                $column = 'dispatch_date';
            } elseif ($request->status == '4') {
                $column = 'gift_recived_date';
            }
        }
        if ($request->status == '0' || $request->status == '2' || $redemption->redeem_mode == '2') {
            $updateStatus = Redemption::where('id', $request->id)->update(['status' => $request->status, 'dispatch_number' => $request->dispatch_number]);
        }else{
            $updateStatus = Redemption::where('id', $request->id)->update(['status' => $request->status, 'dispatch_number' => $request->dispatch_number, $column => now()->toDateString()]);
        }
        if ($updateStatus) {
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
                        $status = 'Pending';
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
            return response()->json(['status' => 'success', 'message' => 'Redemption status change successfully!']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Error in change status of redemption!']);
        }
    }

    public function gift_catalogue(RedemptionGiftsDataTable $dataTable, Request $request)
    {
        abort_if(Gate::denies('gift_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return $dataTable->render('gifts.index');
    }

    public function giftDelivered(Request $request)
    {
        $updateStatus = Redemption::where('id', $request->id)->update(['status' => $request->status, 'remark' => $request->remark]);
        if ($updateStatus) {
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
            return response()->json(['status' => 'success', 'message' => 'Redemption status change successfully!']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Error in change status of redemption!']);
        }
    }

    public function template()
    {
        abort_if(Gate::denies('redemption_template'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new RedemptionTemplate, 'NEFT_Redemption_template.xlsx');
    }

    public function gift_template()
    {
        abort_if(Gate::denies('redemption_template'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new RedemptionGiftTemplate, 'Gift_Redemption_template.xlsx');
    }

    public function upload(Request $request)
    {
        abort_if(Gate::denies('redemption_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        Excel::import(new RedemptionImport, request()->file('import_file'));
        return back()->with('message_success', 'NEFT Redemption Status changed successfully.');
    }
}
