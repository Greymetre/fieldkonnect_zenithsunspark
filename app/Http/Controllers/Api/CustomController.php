<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Auth;
use Illuminate\Support\Facades\DB;

use Validator;
use Gate;
use App\Models\{CustomerType, Customers, EmployeeDetail};
use App\Models\CustomerDetails;
use App\Models\Division;
use App\Models\LoyaltyAppSetting;


class CustomController extends Controller
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
    public function getCustomerTypeList(Request $request)
    {
        try {
            $pageSize = $request->input('pageSize');
            $query = CustomerType::where(function ($query) {
                $query->where('active', '=', 'Y');
                $query->where('type_name', '=', 'retailer');
            })->select('id', 'customertype_name');

            $db_data = (!empty($pageSize)) ? $query->paginate($pageSize) : $query->get();
            $data = collect([]);
            if ($db_data->isNotEmpty()) {
                foreach ($db_data as $key => $value) {
                    $data->push([
                        'customertype' => isset($value['id']) ? $value['id'] : 0,
                        'customertype_name' => isset($value['customertype_name']) ? $value['customertype_name'] : '',
                    ]);
                }
                return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $data], $this->successStatus);
            }
            return response(['status' => 'error', 'message' => 'No Record Found.', 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }
    public function getReportType(Request $request)
    {
        try {
            $data = collect([
                'Field Activity',
                'Tour Programme'
            ]);
            if ($data->isNotEmpty()) {
                return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $data], $this->successStatus);
            }
            return response(['status' => 'error', 'message' => 'No Record Found.', 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }
    public function getWorkType(Request $request)
    {
        try {
            $data = collect([
                collect(["type" => 'Tour', "is_city" => true, "is_beat" => true, 'image' => true, 'summary' => true, 'city_required' => true, 'beat_required' => true]),
                collect(["type" => 'Office', "is_city" => true, "is_beat" => false, 'image' => true, 'summary' => true, 'city_required' => true, 'beat_required' => false]),
                // collect(["type" => 'Plumber Meet', "is_city" => true, "is_beat" => false, 'image' => false, 'summary' => false, 'city_required' => false, 'beat_required' => false]),
                // collect(["type" => 'Service Center Visit', "is_city" => true, "is_beat" => false, 'image' => false, 'summary' => false, 'city_required' => false, 'beat_required' => false]),
                collect(["type" => 'Local Market Visit', "is_city" => true, "is_beat" => false, 'image' => false, 'summary' => false, 'city_required' => false, 'beat_required' => false]),
                collect(["type" => 'Customer Visit', "is_city" => true, "is_beat" => false, 'image' => false, 'summary' => false, 'city_required' => false, 'beat_required' => false]),

                collect(["type" => 'Other', "is_city" => true, "is_beat" => false, 'image' => false, 'summary' => false, 'city_required' => false, 'beat_required' => false]),


            ]);
            if ($data->isNotEmpty()) {
                return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $data], $this->successStatus);
            }
            return response(['status' => 'error', 'message' => 'No Record Found.', 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }
    public function mobileNumberExists(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'mobile'  => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' => $validator->messages()->all()], $this->badrequest);
            }
            $mobile = $request->input('mobile');
            if (strlen(preg_replace('/\s+/', '', $mobile)) == 10) {
                $mobile = '91' . preg_replace('/\s+/', '', $mobile);
            }
            if (!$customer = Customers::where('mobile', '=', $mobile)->exists()) {

                return response()->json(['status' => 'success', 'message' => 'Mobile number is available.'], $this->successStatus);
            }
            return response(['status' => 'error', 'message' => 'Mobile number already exists'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }


    public function gstNumberExists(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'gstnumber'  => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' => $validator->messages()->all()], $this->badrequest);
            }
            $gstnumber = $request->input('gstnumber');

            if (!$customerdetails = CustomerDetails::where('gstin_no', '=', $gstnumber)->exists()) {

                return response()->json(['status' => 'success', 'message' => 'GST number is available.'], $this->successStatus);
            }
            return response(['status' => 'error', 'message' => 'GST number already exists'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }





    public function emailExists(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email'  => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' => $validator->messages()->all()], $this->badrequest);
            }
            $email = $request->input('email');
            if (!$customer = Customers::where('email', '=', $email)->exists()) {

                return response()->json(['status' => 'success', 'message' => 'Email is available.'], $this->successStatus);
            }
            return response(['status' => 'error', 'message' => 'Email already exists'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function getDevision(Request $request)
    {
        // try
        // { 
        //     $data = collect([
        //       collect(["id" => "1", "title" => "Pumps"]), 
        //       collect(["id" => "2", "title" => "Motors"]), 
        //       collect(["id" => "3", "title" => "Fan & Appliance"]), 
        //       collect(["id" => "4", "title" => "Agri"]), 
        //       collect(["id" => "5", "title" => "Solar"]), 
        //       collect(["id" => "6", "title" => "Tender"]), 

        //     ]);


        //     if($data->isNotEmpty())
        //     {
        //         return response()->json(['status' => 'success','message' => 'Data retrieved successfully.','data' => $data ], $this->successStatus);
        //     }
        //     return response(['status' => 'error', 'message' => 'No Record Found.', 'data' => $data ],200); 
        // }
        // catch(\Exception $e)
        // {
        //     return response()->json(['status' => 'error','message' => $e->getMessage() ], $this->internalError);
        // }

        try {
            $pageSize = $request->input('pageSize');
            $query = Division::where(function ($query) {
                $query->where('active', '=', 'Y');
            })->select('id', 'division_name');

            $db_data = (!empty($pageSize)) ? $query->paginate($pageSize) : $query->get();
            $data = collect([]);
            if ($db_data->isNotEmpty()) {
                foreach ($db_data as $key => $value) {
                    $data->push([
                        'id' => isset($value['id']) ? $value['id'] : '',
                        'title' => $value['division_name'] ?? '',

                    ]);
                }
                return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $data], $this->successStatus);
            }
            return response(['status' => 'error', 'message' => 'No Record Found.', 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }


    public function getRetailerList(Request $request)
    {
        try {
            $pageSize = $request->input('pageSize');
            $search = $request->input('search');
            $pageSize = $pageSize ?? 1000; // Default page size if not provided
            $page = $request->input('page', 1); // Get the current page, default to 1
            $chunkSize = 500; // Size of each chunk for the 'whereIn' condition
            $data = collect([]);

            $user = Auth::guard('users')->user();
            if ($user) {
                $user_id = $user->id;
                $all_user_ids = getUsersReportingToAuth($user_id);
                $customer_ids_assign = EmployeeDetail::whereIn('user_id', $all_user_ids)->pluck('customer_id')->toArray();
            } else {
                $customer_ids_assign = [];
            }

            $query = Customers::where(function ($query) use ($customer_ids_assign, $search, $user) {
                $query->where('active', '=', 'Y')->whereIn('customertype', ['1', '3']);
                if (!empty($customer_ids_assign)) {
                    if(!$user->hasRole('superadmin') && !$user->hasRole('Admin') && !$user->hasRole('Sub_Admin'))
                    {
                        $query->whereIn('id', $customer_ids_assign);
                    }
                }
                if ($search) {
                    $query->where('name', 'LIKE', '%' . $search . '%');
                }
            })->select('id', 'name');


            // dd($query->toSql());

            // Handle pagination manually due to chunking
            if (!empty($customer_ids_assign)) {
                $chunkedData = collect();
                $chunks = array_chunk($customer_ids_assign, $chunkSize);

                foreach ($chunks as $chunk) {
                    $chunkQuery = clone $query;
                    $db_data_chunk = $chunkQuery->whereIn('id', $chunk)->get();

                    if ($db_data_chunk->isNotEmpty()) {
                        foreach ($db_data_chunk as $value) {
                            $chunkedData->push([
                                'id' => $value->id ?? '',
                                'name' => $value->name ?? '',
                            ]);
                        }
                    }
                }

                // Paginate manually from the collected data
                $total = $chunkedData->count();
                $paginatedData = $chunkedData->forPage($page, $pageSize);
            } else {
                // If no customer IDs, run the query without the 'whereIn' condition
                $db_data = $query->paginate($pageSize);

                $total = $db_data->total();
                $paginatedData = collect();

                foreach ($db_data as $value) {
                    $paginatedData->push([
                        'id' => $value->id ?? '',
                        'name' => $value->name ?? '',
                    ]);
                }
            }
            if ($paginatedData->isNotEmpty()) {
                $paginatedData = $paginatedData->values();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Data retrieved successfully.',
                    'data' => $paginatedData,
                    'current_page' => $page,
                    'total' => $total,
                    'last_page' => ceil($total / $pageSize),
                    'per_page' => $pageSize,
                ], 200);
            }
            return response(['status' => 'error', 'message' => 'No Record Found.', 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function getslider()
    {
        try {
            $data = LoyaltyAppSetting::first();
            $slider_image = $data->getMedia('slider_image');
            $gift_slider_image = $data->getMedia('gift_slider_image');
            $side_menu_image = $data->getFirstMedia('loyalty_side_menu_image');
            if ($side_menu_image) {
                $main_data['loyalty_side_menu_image'] = $side_menu_image->getFullUrl();
            }else{
                $main_data['loyalty_side_menu_image'] = null;
            }
            if (count($slider_image) > 0) {
                $k = 0;
                foreach ($slider_image as $val) {
                    $main_data['slider_image'][$k] = $val->original_url;
                    $k++;
                }
            }
            if (count($gift_slider_image) > 0) {
                $k = 0;
                foreach ($gift_slider_image as $val) {
                    $main_data['gift_slider_image'][$k] = $val->original_url;
                    $k++;
                }
            }
            return response(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $main_data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }
}
