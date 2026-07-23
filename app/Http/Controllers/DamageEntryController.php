<?php

namespace App\Http\Controllers;

use Illuminate\Validation\ValidationException;
use App\DataTables\DamageEntryDataTable;
use Illuminate\Support\Facades\Redirect;
use App\Models\Customers;
use App\Models\DamageEntry;
use App\Models\Product;
use App\Models\SchemeDetails;
use App\Models\Services;
use App\Models\TransactionHistory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Exports\DamageEntriesExport;
use Gate;
use Validator;
use Excel;

class DamageEntryController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->DamageEntry = new DamageEntry();
        $this->path = 'DamageEntry';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(DamageEntryDataTable $dataTable)
    {
        abort_if(Gate::denies('damage_entry_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return $dataTable->render('damage_entry.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_if(Gate::denies('damage_entry_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view('damage_entry.create')->with('DamageEntry', $this->DamageEntry);
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
            abort_if(Gate::denies('damage_entry_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
            $validator = Validator::make($request->all(), [
                'customer_id' => 'required',
                'damageattach1' => 'required|image',
            ]);
            $validator->setCustomMessages([
                'damageattach1.required' => 'Please attach at least one attachment.',
            ]);
            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }
            $expire_schemes = array();
            if ($request->coupen_code && $request->coupen_code != NULL && $request->coupen_code != '') {
                $exists = TransactionHistory::where('coupon_code', $request->coupen_code)->exists();
                $existsDamage = DamageEntry::where('coupon_code', $request->coupen_code)->exists();

                if ($exists) {
                    throw ValidationException::withMessages([
                        'coupon_code' => "The coupon code '$request->coupen_code' already Scanned.",
                    ]);
                }
                if ($existsDamage) {
                    throw ValidationException::withMessages([
                        'coupon_code' => "The coupon code '$request->coupen_code' already Scanned.",
                    ]);
                }
                $scheme = Services::where('serial_no', $request->coupen_code)->first();
                $scheme_details = SchemeDetails::where('product_id', $scheme->product?->id)->first();
                if ($scheme && $scheme_details) {
                    $scheme_id = $scheme_details->scheme_id;
                    $start_date = Carbon::createFromFormat('Y-m-d', $scheme_details->scheme->start_date);
                    $end_date = Carbon::createFromFormat('Y-m-d', $scheme_details->scheme->end_date);
                    $current_date = Carbon::today();
                    if ($current_date->isSameDay($start_date) || ($current_date->gte($start_date) && $current_date->lte($end_date))) {
                        $point = ($scheme_details) ? $scheme_details->points : NULL;
                    } else {
                        array_push($expire_schemes, $request->coupen_code);
                        $point = '0';
                    }
                } else {
                    $scheme_id = NULL;
                    $point = 0;
                }
                $damageEntry = new DamageEntry();
                $damageEntry->customer_id = $request->customer_id;
                $damageEntry->coupon_code = $request->coupen_code;
                $damageEntry->scheme_id = $scheme_id;
                $damageEntry->point = $point;
                $damageEntry->created_by = auth()->user()->id;
                $damageEntry->save();
            } else {
                $damageEntry = new DamageEntry();
                $damageEntry->customer_id = $request->customer_id;
                $damageEntry->created_by = auth()->user()->id;
                $damageEntry->save();
            }

            if ($request->hasFile('damageattach1')) {
                $file = $request->file('damageattach1');
                $customname = time() . '.' . $file->getClientOriginalExtension();
                $damageEntry->addMedia($file)
                    ->usingFileName($customname)
                    ->toMediaCollection('damageattach1');
            }
            if ($request->hasFile('damageattach2')) {
                $file = $request->file('damageattach2');
                $customname = time() . '.' . $file->getClientOriginalExtension();
                $damageEntry->addMedia($file)
                    ->usingFileName($customname)
                    ->toMediaCollection('damageattach2');
            }
            if ($request->hasFile('damageattach3')) {
                $file = $request->file('damageattach3');
                $customname = time() . '.' . $file->getClientOriginalExtension();
                $damageEntry->addMedia($file)
                    ->usingFileName($customname)
                    ->toMediaCollection('damageattach3');
            }
            if (count($expire_schemes) > 0) {
                return Redirect::to('damage_entries')->with('message_info', 'Damage Entry Store Successfully but coupon code (' . implode(',', $expire_schemes) . ') scheme has either expired or has not started yet so you earned 0 point.');
            } else {
                return Redirect::to('damage_entries')->with('message_success', 'Damage Entry Store Successfully');
            }
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DamageEntry  $damageEntry
     * @return \Illuminate\Http\Response
     */
    public function show(DamageEntry $damageEntry)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\DamageEntry  $damageEntry
     * @return \Illuminate\Http\Response
     */
    public function edit(DamageEntry $damageEntry)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\DamageEntry  $damageEntry
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DamageEntry $damageEntry)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DamageEntry  $damageEntry
     * @return \Illuminate\Http\Response
     */
    public function destroy(DamageEntry $damageEntry)
    {
        //
    }

    public function changeStatus(Request $request)
    {
        $updateStatus = DamageEntry::where('id', $request->id)->update(['status' => $request->status, 'coupon_code' => $request->coupon_code, 'remark' => $request->remark]);
        if ($updateStatus) {
            if ($request->status == '1') {
                $damageEntry = DamageEntry::find($request->id);
                $damageEntry->status = '1';
                $damageEntry->save();
                $product = Product::find($request->product_id);
                $notexists = Services::where('serial_no', $request->coupon_code)->exists();
                if (!$notexists) {
                    // $mmssgg = "The coupon code '$request->coupon_code' already Scanned.";
                    // return response()->json(['status' => 'error','message' => $mmssgg]);
                    Services::create([
                        'product_code' => $product->product_code,
                        'product_name' => $product->product_name,
                        'product_description' => $product->description,
                        'group' => $product->new_group,
                        'serial_no' => $request->coupon_code,
                        'party_name' => 'Damage Entry',
                        'qty' => '1',
                    ]);
                }
                $exists = TransactionHistory::where('coupon_code', $request->coupon_code)->exists();

                if ($exists) {
                    $mmssgg = "The coupon code '$request->coupon_code' already Scanned.";
                    return response()->json(['status' => 'error', 'message' => $mmssgg]);
                }
                $scheme = Services::where('serial_no', $request->coupon_code)->first();
                $scheme_details = SchemeDetails::where('product_id', $scheme?->product?->id)->first();
                $point = 0;
                if ($scheme_details) {
                    $scheme_id = $scheme_details->scheme_id;
                    $start_date = Carbon::createFromFormat('Y-m-d', $scheme_details->scheme->start_date);
                    $end_date = Carbon::createFromFormat('Y-m-d', $scheme_details->scheme->end_date);
                    $current_date = Carbon::today();
                    if ($current_date->isSameDay($start_date) || ($current_date->gte($start_date) && $current_date->lte($end_date))) {
                        $point = ($scheme_details) ? $scheme_details->points : NULL;
                    } else {
                        // array_push($expire_schemes, $request->coupon_code);
                        $point = '0';
                    }
                } else {
                    $scheme_id = null;
                    $point = '0';
                }
                $created_at = Carbon::now();
                $created_at = $created_at->setTimezone('Asia/Kolkata');
                if (!empty($request->coupon_code)) {
                    if ($scheme_details) {
                        $start_date = Carbon::createFromFormat('Y-m-d', $scheme_details->scheme->start_date);
                        $end_date = Carbon::createFromFormat('Y-m-d', $scheme_details->scheme->end_date);
                        $current_date = Carbon::today();
                        if ($current_date->isSameDay($start_date) || ($current_date->gte($start_date) && $current_date->lte($end_date))) {
                            $active_point = ($scheme_details) ? $scheme_details->active_point : NULL;
                            $provision_point = ($scheme_details) ? $scheme_details->provision_point : NULL;
                            $point = ($scheme_details) ? $scheme_details->points : NULL;
                            $scheme_id = $scheme_details?->scheme_id;
                        }
                    } else {
                        // array_push($expire_schemes, $request->coupon_code);
                        $active_point = '0';
                        $provision_point = '0';
                        $point = '0';
                        $scheme_id = null;
                    }
                    $tHistory = TransactionHistory::create([
                        'customer_id' => $damageEntry->customer_id,
                        'coupon_code' => $request->coupon_code,
                        'scheme_id' => $scheme_id,
                        'active_point' => $active_point,
                        'provision_point' => $provision_point,
                        'point' => $point,
                        'remark' => 'Coupon scan',
                        'created_by' => auth()->user()->id,
                    ]);
                } else {
                    $tHistory = TransactionHistory::create([
                        'customer_id' => $damageEntry->customer_id,
                        'coupon_code' => $request->coupon_code,
                        'scheme_id' => $scheme_details ? $scheme_id : null,
                        'point' => $point,
                        'created_at' => $created_at,
                    ]);
                }
                return response()->json(['status' => 'success', 'message' => 'Damage Entry Approved successfully!']);
            } elseif ($request->status == '0') {
                return response()->json(['status' => 'success', 'message' => 'Damage Entry Pending successfully!']);
            } else {
                return response()->json(['status' => 'success', 'message' => 'Damage Entry Rejected successfully!']);
            }
        } else {
            return response()->json(['status' => 'error', 'message' => 'Error in change status of Damage Entry!']);
        }
    }

    /*
    Damage entries export
    */
    public function damage_entries_download(Request $request)
    {
        abort_if(Gate::denies('damage_entry_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();

        return Excel::download(new DamageEntriesExport($request), 'damage_entry.xlsx');
    }
}
