<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GiftCategory;
use App\Models\Gifts;
use App\Models\GiftSubcategory;
use Illuminate\Http\Request;
use Validator;

class GiftController extends Controller
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

    public function getgiftcatalogue(Request $request)
    {
        try {
            $pageSize = $request->input('pageSize');
            $query = Gifts::with('brands')->where(function ($query) use ($request) {
                $query->where('active', '=', 'Y');
                if ($request->search && $request->search != '' && $request->search != null) {
                    $query->where('product_name', 'LIKE', "%{$request->search}%")->orWhere('display_name', 'LIKE', "%{$request->search}%");
                }
                if ($request->category_id && $request->category_id != '' && $request->category_id != null) {
                    $query->where('category_id', $request->category_id);
                }
                if ($request->subcategory_id && $request->subcategory_id != '' && $request->subcategory_id != null) {
                    $query->where('subcategory_id', $request->subcategory_id);
                }
                if ($request->min_range && $request->min_range != '' && $request->min_range != null && $request->max_range && $request->max_range != '' && $request->max_range != null) {
                    $query->whereBetween('points', [$request->min_range, $request->max_range]);
                }
            })->orderBy('points', 'asc');
            $db_data = (!empty($pageSize)) ? $query->paginate($pageSize) : $query->get();

            $data = collect([]);
            if ($db_data->isNotEmpty()) {
                foreach ($db_data as $key => $value) {
                    $data->push([
                        'id' => isset($value['id']) ? $value['id'] : 0,
                        'product_name' => isset($value['product_name']) ? $value['product_name'] : '',
                        'display_name' => isset($value['display_name']) ? $value['display_name'] : '',
                        'brand' => isset($value['brands']) ? $value['brands']['brand_name'] : '',
                        'points' => isset($value['points']) ? $value['points'] : '',
                        'description' => isset($value['description']) ? $value['description'] : '',
                        'product_image' => isset($value['product_image']) ? $value['product_image'] : '',
                    ]);
                }
                return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'image_base_url' => url('public/uploads/'), 'data' => $data], $this->successStatus);
            }
            return response(['status' => 'error', 'message' => 'No Record Found.', 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function getgiftcategories(Request $request)
    {
        try {
            $pageSize = $request->input('pageSize');
            $query = GiftCategory::where(function ($query) use ($request) {
                $query->where('active', '=', 'Y');
            })->latest();
            $db_data = (!empty($pageSize)) ? $query->paginate($pageSize) : $query->get();

            $data = collect([]);
            if ($db_data->isNotEmpty()) {
                foreach ($db_data as $key => $value) {
                    $data->push([
                        'id' => isset($value['id']) ? $value['id'] : 0,
                        'category_name' => isset($value['category_name']) ? $value['category_name'] : '',
                        'category_image' => isset($value['category_image']) ? $value['category_image'] : '',
                    ]);
                }
                return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'image_base_url' => url('public/uploads/'), 'data' => $data], $this->successStatus);
            }
            return response(['status' => 'error', 'message' => 'No Record Found.', 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function getgiftsubcategories(Request $request)
    {
        try {
            $pageSize = $request->input('pageSize');
            $query = GiftSubcategory::where(function ($query) use ($request) {
                $query->where('active', '=', 'Y');
                if ($request->category_id && $request->category_id != '' && $request->category_id != null) {
                    $query->where('category_id', $request->category_id);
                }
            })->latest();
            $db_data = (!empty($pageSize)) ? $query->paginate($pageSize) : $query->get();

            $data = collect([]);
            if ($db_data->isNotEmpty()) {
                foreach ($db_data as $key => $value) {
                    $data->push([
                        'id' => isset($value['id']) ? $value['id'] : 0,
                        'sub_category_name' => isset($value['subcategory_name']) ? $value['subcategory_name'] : '',
                        'sub_category_image' => isset($value['subcategory_image']) ? $value['subcategory_image'] : '',
                    ]);
                }
                return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'image_base_url' => url('public/uploads/'), 'data' => $data], $this->successStatus);
            }
            return response(['status' => 'error', 'message' => 'No Record Found.', 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function getgiftdetails(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'gift_id'  => "required",
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
            }
            $data = Gifts::with('brands')->find($request->gift_id);
            if($data){
                $data['related_products'] = Gifts::with('brands')->where('category_id', $data->category_id)->where('id', '!=', $data->id)->latest('id')->take(5)->get();
                return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'image_base_url' => url('public/uploads/'), 'data' => $data], $this->successStatus);
            }else{
                return response(['status' => 'error', 'message' => 'No Record Found.', 'data' => $data ],200);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }
}
