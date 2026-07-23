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
use App\Models\{State, District, City, Customers, Pincode};

class AddressController extends Controller
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
    public function getStateList(Request $request)
    {
        try {
            $pageSize = $request->input('pageSize');
            $country_id = $request->input('country_id');
            $query = State::where(function ($query) use ($country_id) {
                if (!empty($country_id)) {
                    $query->where('country_id', '=', $country_id);
                }
            })->select('id', 'state_name');

            $db_data = (!empty($pageSize)) ? $query->paginate($pageSize) : $query->get();
            $data = collect([]);
            if ($db_data->isNotEmpty()) {
                foreach ($db_data as $key => $value) {
                    $data->push([
                        'state_id' => isset($value['id']) ? $value['id'] : 0,
                        'state_name' => isset($value['state_name']) ? $value['state_name'] : '',
                    ]);
                }
                return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $data], $this->successStatus);
            }
            return response(['status' => 'error', 'message' => 'No Record Found.', 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function getDistrictList(Request $request)
    {
        try {
            $pageSize = $request->input('pageSize');
            $state_id = $request->input('state_id');
            $query = District::where(function ($query) use ($state_id) {
                if (!empty($state_id)) {
                    $query->where('state_id', '=', $state_id);
                }
            })->select('id', 'district_name');

            $db_data = (!empty($pageSize)) ? $query->paginate($pageSize) : $query->get();
            $data = collect([]);
            if ($db_data->isNotEmpty()) {
                foreach ($db_data as $key => $value) {
                    $data->push([
                        'district_id' => isset($value['id']) ? $value['id'] : 0,
                        'district_name' => isset($value['district_name']) ? $value['district_name'] : '',
                    ]);
                }
                $dis_data = Customers::where('active', '=', 'Y')
                    ->whereIn('customertype', ['1', '3']);
                    if($state_id && !empty($state_id)){
                        $dis_data->whereHas('customeraddress', function ($query) use ($state_id) {
                            $query->where('state_id', $state_id);
                        });
                    }
                    $dis_data = $dis_data->select('id', 'name')->get();
                return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $data, 'dis_data' => $dis_data], $this->successStatus);
            }
            return response(['status' => 'error', 'message' => 'No Record Found.', 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }
    public function getCityList(Request $request)
    {
        try {
            $pageSize = $request->input('pageSize');
            $district_id = $request->input('district_id');
            $query = City::where(function ($query) use ($district_id) {
                if (!empty($district_id)) {
                    $query->where('district_id', '=', $district_id);
                }
            })->select('id', 'city_name');

            $db_data = (!empty($pageSize)) ? $query->paginate($pageSize) : $query->get();
            $data = collect([]);
            if ($db_data->isNotEmpty()) {
                foreach ($db_data as $key => $value) {
                    $data->push([
                        'city_id' => isset($value['id']) ? $value['id'] : 0,
                        'city_name' => isset($value['city_name']) ? $value['city_name'] : '',
                    ]);
                }
                return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $data], $this->successStatus);
            }
            return response(['status' => 'error', 'message' => 'No Record Found.', 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }
    public function getPincodeList(Request $request)
    {
        try {
            $pageSize = $request->input('pageSize');
            $city_id = $request->input('city_id');
            $query = Pincode::where(function ($query) use ($city_id) {
                if (!empty($city_id)) {
                    $query->where('city_id', '=', $city_id);
                }
            })->select('id', 'pincode');

            $db_data = (!empty($pageSize)) ? $query->paginate($pageSize) : $query->get();
            $data = collect([]);
            if ($db_data->isNotEmpty()) {
                foreach ($db_data as $key => $value) {
                    $data->push([
                        'pincode_id' => isset($value['id']) ? $value['id'] : 0,
                        'pincode' => isset($value['pincode']) ? $value['pincode'] : '',
                    ]);
                }
                return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $data], $this->successStatus);
            }
            return response(['status' => 'error', 'message' => 'No Record Found.', 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }
    public function getPincodeInfo(Request $request)
    {
        try {
            $pincode = $request->input('pincode');
            $data = Pincode::where('pincode', '=', $pincode)->select('id', 'city_id')->first();
            if (!empty($data)) {
                $data['city_id'] = isset($data['city_id']) ? $data['city_id'] : 0;
                $data['city_name'] = isset($data['cityname']['city_name']) ? $data['cityname']['city_name'] : '';
                $data['district_id'] = isset($data['cityname']['district_id']) ? $data['cityname']['district_id'] : 0;
                $data['district_name'] = isset($data['cityname']['districtname']['district_name']) ? $data['cityname']['districtname']['district_name'] : '';
                $data['state_id'] = isset($data['cityname']['districtname']['state_id']) ? $data['cityname']['districtname']['state_id'] : 0;
                $data['state_name'] = isset($data['cityname']['districtname']['statename']['state_name']) ? $data['cityname']['districtname']['statename']['state_name'] : '';
                $data['country_id'] = isset($data['cityname']['districtname']['statename']['country_id']) ? $data['cityname']['districtname']['statename']['country_id'] : 0;
                $data['country_name'] = isset($data['cityname']['districtname']['statename']['countryname']['country_name']) ? $data['cityname']['districtname']['statename']['countryname']['country_name'] : 0;
                unset($data['cityname']);
                $dis_data = Customers::where('active', '=', 'Y')
                    ->whereIn('customertype', ['1', '3'])
                    ->whereHas('customeraddress', function ($query) use ($data) {
                        $query->where('district_id', $data['district_id']);
                    })
                    ->select('id', 'name')->get();
                return response(['status' => 'success', 'message' => 'Record Found.', 'data' => $data, 'dis_data' => $dis_data], 200);
            }
            return response(['status' => 'error', 'message' => 'No Record Found.', 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }
}
