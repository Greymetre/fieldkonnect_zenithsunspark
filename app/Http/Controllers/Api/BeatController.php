<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Validator;
use Gate;
use App\Models\Beat;
use App\Models\BeatCustomer;
use App\Models\BeatSchedule;
use App\Models\CheckIn;
use App\Models\Customers;
use Carbon\Carbon;

class BeatController extends Controller
{
    public function __construct()
    {
        $this->beats = new Beat();

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

    public function getBeatList(Request $request)
    {
        try {
            $user_id = $request->user()->id;
            $pageSize = $request->input('pageSize');
            $beatDate = !empty($request->input('beatDate')) ? getcurentDate() : '';
            $query = BeatSchedule::with('beats', 'beatcheckininfo')->withCount(['beatcustomers as total_customers', 'beatcheckininfo as visited_customers', 'beatscheduleorders as order_count', 'beatschedulecustomer as new_customers'])
                ->where(function ($query) use ($user_id, $request) {
                    if (!empty($request['city_id'])) {
                        $cityids = explode(',', preg_replace('/\s*,\s*/', ',', $request['city_id']));
                        $query->whereHas('beats', function ($query) use ($cityids) {
                            $query->whereIn('city_id', $cityids);
                        });
                    }
                    $query->where('user_id', $user_id);
                    $query->whereDate('beat_date', '>=', date('Y-m-d'));
                });
            $db_data = (!empty($pageSize)) ? $query->paginate($pageSize) : $query->get();
            $data = collect([]);
            if ($db_data->isNotEmpty()) {
                $beats = $db_data->map(function ($item, $key) {
                    $item['beat_name'] = isset($item['beats']['beat_name']) ? $item['beats']['beat_name'] : '';
                    $item['beatscheduleid'] = isset($item['id']) ? $item['id'] : null;
                    $item['description'] = isset($item['beats']['description']) ? $item['beats']['description'] : '';
                    $item['visited_customers'] = $item['beatcheckininfo']->unique('customer_id', 'checkin_date')->count();
                    $item['remaining_customers'] = $item['total_customers'] - $item['visited_customers'];
                    $item['is_today'] = $item['beat_date'] == date('Y-m-d') ? true : false;
                    unset($item["id"], $item["active"], $item['user_id'], $item['created_at'], $item['updated_at'], $item['beats']);
                    return $item;
                });
                return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $beats], $this->successStatus);
            }
            return response(['status' => 'error', 'message' => 'No Record Found.', 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function getBeatDropdownList(Request $request)
    {
        try {
            $user_id = $request->user()->id;
            $beats = Beat::whereHas('beatusers', function ($query) use ($user_id) {
                $query->where('user_id', '=', $user_id);
            })
                ->where(function ($query) use ($request) {
                    if (!empty($request['city_id'])) {
                        $cityids = explode(',', preg_replace('/\s*,\s*/', ',', $request['city_id']));
                        foreach ($cityids as $city_id) {
                            $query->orWhereRaw("FIND_IN_SET(?, city_id)", [$city_id]);
                        }
                    }
                })
                ->select('id as beat_id', 'beat_name', 'city_id')
                ->orderBy('city_id', 'asc')
                ->get();
            if ($beats->isNotEmpty()) {
                return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $beats], $this->successStatus);
            }
            return response(['status' => 'error', 'message' => 'No Record Found.', 'data' => $beats], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function getBeatCustomers(Request $request)
    {
        try {
            $user = $request->user();
            // $user_id = $user->id;
            $pageSize = $request->input('pageSize');
            $beat_id = $request->input('beat_id');
            $latitude = $request->input('latitude');
            $longitude = $request->input('longitude');
            $search = $request->input('search');
            $checkedin = CheckIn::where('user_id', '=', $user->id)->whereDate('checkin_date', '=', date('Y-m-d'))->pluck('customer_id')->toArray();
            $query = Customers::with('customeraddress:customer_id,address1,address2', 'customerdetails:customer_id,grade,visit_status', 'customertypes')
                ->where(function ($query) use ($search, $beat_id) {
                    if (!empty($search)) {
                        $query->where('name', 'like', "%{$search}%")
                            ->Orwhere('first_name', 'like', "%{$search}%")
                            ->Orwhere('last_name', 'like', "%{$search}%")
                            ->Orwhere('mobile', 'like', "%{$search}%");
                    }
                    $query->whereHas('beatdetails', function ($query) use ($beat_id) {
                        $query->where('beat_id', '=', $beat_id);
                    });
                })
                ->select('id', 'name', 'mobile', 'email', 'profile_image', 'latitude', 'longitude', 'customertype')
                ->orderBy('name', 'asc');
            //->latest();
            $db_data = (!empty($pageSize)) ? $query->paginate($pageSize) : $query->get();
            $leadcustomers = collect([]);
            $collection = collect([]);
            if ($db_data->isNotEmpty()) {
                foreach ($db_data as $key => $value) {
                    $collection->push([
                        'customer_id' => isset($value['id']) ? $value['id'] : 0,
                        'address' => isset($value['customeraddress']['address1']) ? $value['customeraddress']['address1'] . ' ' . $value['customeraddress']['address2'] : '',
                        'name' => isset($value['name']) ? $value['name'] : '',
                        'mobile' => isset($value['mobile']) ? $value['mobile'] : '',
                        'email' => isset($value['email']) ? $value['email'] : '',
                        'profile_image' => isset($value['profile_image']) ? $value['profile_image'] : '',
                        'latitude' => isset($value['latitude']) ? $value['latitude'] : '',
                        'longitude' => isset($value['longitude']) ? $value['longitude'] : '',
                        'grade' => isset($value['customerdetails']['grade']) ? $value['customerdetails']['grade'] : '',
                        'visit_status' => isset($value['customerdetails']['visit_status']) ? $value['customerdetails']['visit_status'] : '',
                        'customer_type' => isset($value['customertypes']['customertype_name']) ? $value['customertypes']['customertype_name'] : '',
                        'isvisited' => in_array($value['id'], $checkedin) ? true : false
                    ]);
                }
            }
            $data = $collection->sortBy('isvisited')->values();
            // $query = BeatCustomer::with(['beats','beatschedules','customers' => function($query) use($latitude , $longitude) {
            //                             $query->select('id', 'name','mobile','profile_image','latitude','longitude','email','first_name','last_name','customer_code','customertype',
            // // "( 6371 * acos( cos( radians(" . $latitude . ") ) *
            // // cos( radians(customers.latitude) ) *
            // // cos( radians(customers.longitude) - radians(" . $longitude . ") ) + 
            // // sin( radians(" . $latitude . ") ) *
            // // sin( radians(customers.latitude) ) ) ) 
            // // AS distance", 
            //        DB::raw('(SELECT SUM(grand_total) FROM sales WHERE sales.buyer_id = customers.id) as totalamount'), 
            //        DB::raw('(SELECT SUM(paid_amount) FROM sales WHERE sales.buyer_id = customers.id) as totalpaid'));
            //                         }])
            //                         ->whereHas('beatschedules', function ($query) use($user) {
            //                             $query->where('user_id', '=', $user->id);
            //                             // $query->whereDate('beat_date', '=', date('Y-m-d'));
            //                          //$query->where('active', '=','Y');
            //                         })
            //                         ->where(function ($query) use($beat_id) {
            //                             if(!empty($beat_id))
            //                             {
            //                                 $query->where('beat_id', '=', $beat_id);
            //                             }
            //                         })
            //                         ->whereNotNull('customer_id')
            //                         ->select('id','customer_id','beat_id')->latest();
            // $db_data = (!empty($pageSize)) ? $query->paginate($pageSize) : $query->get();
            // $data = collect([]);

            // $leadcustomers = collect([]);

            // if($db_data->isNotEmpty())
            // {
            //     foreach ($db_data as $key => $value) {
            //         $data->push([
            //             'beat_detail_id' => isset($value['id']) ? $value['id'] : 0,
            //             'customer_id' => isset($value['customer_id']) ? $value['customer_id'] : 0,
            //             'customer_name' => isset($value['customers']['name']) ? $value['customers']['name'] : '',
            //             'customer_mobile' => isset($value['customers']['mobile']) ? $value['customers']['mobile'] : '',
            //             'profile_image' => isset($value['customers']['profile_image']) ? $value['customers']['profile_image'] : '',
            //             'latitude' => isset($value['customers']['latitude']) ? $value['customers']['latitude'] : '',
            //             'longitude' => isset($value['customers']['longitude']) ? $value['customers']['longitude'] : '',
            //             'address' => isset($value['customers']['customeraddress']['address1']) ? $value['customers']['customeraddress']['address1'].' '.$value['customers']['customeraddress']['address2'] : '',
            //             'beat_name' => isset($value['beats']['beat_name']) ? $value['beats']['beat_name'] : '',
            //             'beat_date' => isset($value['beatschedules']['beat_date']) ? $value['beatschedules']['beat_date'] : '',
            //             'description' => isset($value['beats']['description']) ? $value['beats']['description'] : '',
            //             'outstanding' => $value['customers']['totalamount']-$value['customers']['totalpaid'],
            //             'name' => isset($value['customers']['name']) ? $value['customers']['name'] : '',
            //             'first_name' => isset($value['customers']['first_name']) ? $value['customers']['first_name'] : '',
            //             'last_name' => isset($value['customers']['last_name']) ? $value['customers']['last_name'] : '',
            //             'mobile' => isset($value['customers']['mobile']) ? $value['customers']['mobile'] : '',
            //             'email' => isset($value['customers']['email']) ? $value['customers']['email'] : '',
            //             'profile_image' => isset($value['customers']['profile_image']) ? $value['customers']['profile_image'] : '',
            //             'customer_code' => isset($value['customers']['customer_code']) ? $value['customers']['customer_code'] : '',
            //             'totalamount' => isset($value['customers']['totalamount']) ? $value['customers']['totalamount'] : '',
            //             'totalpaid' => isset($value['customers']['totalpaid']) ? $value['customers']['totalpaid'] : '',
            //             'outstanding' => $value['totalamount']-$value['totalpaid'],
            //             'address1' => isset($value['customers']['customeraddress']['address1']) ? $value['customers']['customeraddress']['address1'] : '',
            //             'address2' => isset($value['customers']['customeraddress']['address2']) ? $value['customers']['customeraddress']['address2'] : '',
            //             'latitude' => isset($value['customers']['latitude']) ? $value['customers']['latitude'] : '',
            //             'longitude' => isset($value['customers']['longitude']) ? $value['customers']['longitude'] : '',
            //             'shop_image' => isset($value['customers']['customerdetails']['shop_image']) ? $value['customers']['customerdetails']['shop_image'] : '',
            //             'visiting_card' => isset($value['customers']['customerdetails']['visiting_card']) ? $value['customers']['customerdetails']['visiting_card'] : '',
            //             'grade' => isset($value['customers']['customerdetails']['grade']) ? $value['customers']['customerdetails']['grade'] : '',
            //             'visit_status' => isset($value['customers']['customerdetails']['visit_status']) ? $value['customers']['customerdetails']['visit_status'] : '',
            //             'customer_type' => isset($value['customers']['customertypes']['customertype_name']) ? $value['customers']['customertypes']['customertype_name'] : '',
            //             'distance' => isset($value['customers']['distance']) ? $value['customers']['distance'] : '',
            //         ]);
            //     }

            // }
            if ($data->isNotEmpty() || $leadcustomers->isNotEmpty()) {
                return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $data, 'leads' => $leadcustomers], $this->successStatus);
            }
            return response(['status' => 'error', 'message' => 'No Record Found.', 'data' => $data, 'leads' => $leadcustomers], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function userScheduleBeat(Request $request)
    {
        try {
            $userid = $request->user()->id;
            $validator = Validator::make($request->all(), [
                'beats.*'  => "required",
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
            }
            $collection = array();
            if (is_array($request['beats'])) {
                foreach ($request['beats'] as $key => $beat) {
                    array_push($collection, array(
                        "user_id" => $userid,
                        'beat_id' => $beat,
                        'beat_date' => date('Y-m-d'),
                        'created_at' => date('Y-m-d H:i:s')
                    ));
                }
            }
            if (BeatSchedule::insert($collection)) {
                return response()->json(['status' => 'success', 'message' => 'Data inserted successfully.'], $this->successStatus);
            }
            return response(['status' => 'error', 'message' => 'Error in No Record Found.'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function getTodaySchedul(Request $request)
    {
        try {
            $userid = $request->user()->id;
            $todayDate = Carbon::today()->toDateString();
            $data = BeatSchedule::with('beats')->where('user_id', $userid)->where('beat_date', $todayDate)->get();

            foreach ($data as $key => $value) {
                $data[$key]['beats']['city_id'] = (string)$value->beats->city_id;
                $data[$key]['beats']['state_id'] = (string)$value->beats->state_id;
                $data[$key]['beats']['district_id'] = (string)$value->beats->district_id;
            }

            return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $data], $this->successStatus);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }
}
