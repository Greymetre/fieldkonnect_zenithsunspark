<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PrimarySales;
use App\Models\Product;
use App\Models\SapStock;
use App\Models\WareHouse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Validator;

class SapStockController extends Controller
{

    public function __construct()
    {
        $this->successStatus = 200;
        $this->created = 201;
        $this->accepted = 202;
        $this->noContent = 204;
        $this->badrequest = 400;
        $this->unauthorized = 401;
        $this->notFound = 404;
        $this->notactive = 406;
        $this->internalError = 500;
    }

    public function insertSapStock(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'itm_code' => 'required',
                'itm_desc' => 'required',
                'itm_grp_code' => 'required',
                'itm_grp_name' => 'required',
                'warehouse_code' => 'required',
                'warehouse_name' => 'required',
                'instock_qty' => 'required',
                'value' => 'required',
                'itm_remarks' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
            }

            $product = Product::where('sap_code', $request['itm_code'])->first();

            // if (!$product) {
            //     Log::channel('sapstock')->error("Product with itm_code '{$request['itm_code']}' is not available in our system.");

            //     return response()->json([
            //         'status' => 'error',
            //         'message' => "Product with itm_code '{$request['itm_code']}' is not available in our system."
            //     ], $this->notFound);
            // } else {
            //     $wareHouse = WareHouse::where('warehouse_code', $request['warehouse_code'])->first();

            //     if (!$wareHouse) {
            //         Log::channel('sapstock')->error("WareHouse with warehouse_code '{$request['warehouse_code']}' is not available in our system.");

            //         return response()->json([
            //             'status' => 'error',
            //             'message' => "WareHouse with warehouse_code '{$request['warehouse_code']}' is not available in our system."
            //         ], $this->notFound);
            //     } else {
            SapStock::updateorCreate([
                'product_sap_code' => $request['itm_code'],
                'warehouse_code' => $request['warehouse_code'],
            ], [
                'product_description' => $request['itm_desc'],
                'product_category_sap_code' => $request['itm_grp_code'],
                'product_category_name' => $request['itm_grp_name'],
                'warehouse_name' => $request['warehouse_name'],
                'instock_qty' => $request['instock_qty'],
                'value' => $request['value'],
                'itm_remarks' => $request['itm_remarks'],
            ]);

            return response()->json([
                'status' => 'success',
                'message' => "Data updated successfully."
            ], $this->successStatus);
            // }
            // }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function insertSapSell(Request $request)
    {
        try {
            // return response()->json($request->branch_code);
            $validator = Validator::make($request->all(), [
                'branch_code' => 'required',
                'sinv_branch' => 'required',
                'document_status' => 'required',
                'canceled' => 'required',
                'sinv_no' => 'required',
                'sinv_dt' => 'required|date_format:dmY', // Validates date format
                'bp_code' => 'required',
                'bp_name' => 'required',
                'billto_city' => 'required',
                'billto_state' => 'required',
                'documentLines' => 'required|array',
                'documentLines.*.item_no' => 'required',
                'documentLines.*.item_desc' => 'required',
                'documentLines.*.group_code' => 'required',
                'documentLines.*.itm_group_name' => 'required',
                'documentLines.*.sinv_total_qty' => 'required|numeric',
                // 'documentLines.*.list_price' => 'required|numeric',
                'documentLines.*.sinv_unit_price' => 'required|numeric',
                'documentLines.*.sinv_price' => 'required|numeric',
                'documentLines.*.tax_code' => 'required',
                'documentLines.*.sinv_gst_amt' => 'required|numeric',
                'emp_code' => 'required',
                'sinv_sales_emp' => 'required',
                'sinv_remarks' => 'required',
                'documentLines.*.serial_no' => 'required|array',
                'documentLines.*.serial_no.*.ItemCode' => 'required',
                'documentLines.*.serial_no.*.Quantity' => 'required|numeric|min:1'
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
            }

            $documentLines = $request['documentLines'];
            $invoice_date = \DateTime::createFromFormat('dmY', $request['sinv_dt']);

            $month = date('M', strtotime(Carbon::parse($invoice_date)->toDateString()));
            $month = strtoupper(substr($month, 0, 3));

            // $itm_group_name_array = explode(' ', $request['itm_group_name']);
            // $division = $itm_group_name_array[0];
            $division = 'PUMP';



            foreach ($documentLines as $key => $value) {
                $itm_group_name_array = explode(' ', $value['itm_group_name']);
                $division = $itm_group_name_array[0];
                $serial_numbers = [];
                foreach ($value['serial_no'] as $serial) {
                    if (isset($serial['DistNumber'], $serial['Quantity'])) {
                        $serial_numbers = array_merge($serial_numbers, array_fill(0, $serial['Quantity'], $serial['DistNumber']));
                    }
                }
                $dist_number_string = implode(',', $serial_numbers);

                PrimarySales::create([
                    'branch_id' => $request['branch_code'],
                    'final_branch' => $request['sinv_branch'],
                    'document_status' => $request['document_status'],
                    'canceled' => $request['canceled'],
                    'invoiceno' => $request['sinv_no'],
                    'invoice_date' => $invoice_date,
                    'month' => $month,
                    'division' => $division,
                    'customer_id' => $request['bp_code'],
                    'dealer' => $request['bp_name'],
                    'city' => $request['billto_city'],
                    'state' => $request['billto_state'],
                    'item_no' => $value['item_no'],
                    'product_name' => $value['item_desc'],
                    'group_code' => $value['group_code'],
                    'itm_group_name' => $value['itm_group_name'],
                    'quantity' => $value['sinv_total_qty'],
                    'lp' => $request['list_price'],
                    'rate' => $value['sinv_unit_price'],
                    'net_amount' => $value['sinv_price'],
                    'tax_code' => $value['tax_code'],
                    'sinv_gst_amt' => $value['sinv_gst_amt'],
                    'emp_code' => $request['emp_code'],
                    'sales_person' => $request['sinv_sales_emp'],
                    'remarks' => $request['sinv_remarks'],
                    'sell_from' => 'sap_api',
                    'serial_no' => $dist_number_string
                ]);
            }


            return response()->json([
                'status' => 'success',
                'message' => "Data updated successfully."
            ], $this->successStatus);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }
}
