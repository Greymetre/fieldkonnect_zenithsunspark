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
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Product;
use App\Models\ProductDetails;
use App\Models\Gift;
use Carbon\Carbon;

class ProductController extends Controller
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

    public function getCategoryList(Request $request)
    {
        try {
            $pageSize = $request->input('pageSize');
            $query = Category::where(function ($query) {
                $query->where('active', '=', 'Y');
            })
                ->select('id', 'category_name', 'category_image')->latest();
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
                return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $data], $this->successStatus);
            }
            return response(['status' => 'error', 'message' => 'No Record Found.', 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function getSubCategoryList(Request $request)
    {
        try {
            $pageSize = $request->input('pageSize');
            $category_id = $request->input('category_id');
            $query = Subcategory::where(function ($query) use ($category_id) {
                if (!empty($category_id)) {
                    $query->where('category_id', '=', $category_id);
                }
                $query->where('active', '=', 'Y');
            })
                ->select('id', 'subcategory_name', 'subcategory_image', 'category_id')->latest();
            $db_data = (!empty($pageSize)) ? $query->paginate($pageSize) : $query->get();
            $data = collect([]);
            if ($db_data->isNotEmpty()) {
                foreach ($db_data as $key => $value) {
                    $data->push([
                        'id' => isset($value['id']) ? $value['id'] : 0,
                        'subcategory_name' => isset($value['subcategory_name']) ? $value['subcategory_name'] : '',
                        'subcategory_image' => isset($value['subcategory_image']) ? $value['subcategory_image'] : '',
                        'category_id' => isset($value['category_id']) ? $value['category_id'] : 0,
                        'category_name' => isset($value['categories']['category_name']) ? $value['categories']['category_name'] : '',
                    ]);
                }
                return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $data], $this->successStatus);
            }
            return response(['status' => 'error', 'message' => 'No Record Found.', 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }
    public function getProductList(Request $request)
    {
        try {
            $pageSize = $request->input('pageSize');
            $category_id = $request->input('category_id');
            $subcategory_id = $request->input('subcategory_id');
            $brand_id = $request->input('brand_id');
            $search = $request->input('search');
            $query = Product::with('productdetails', 'productpriceinfo', 'getSchemeDetail')->where(function ($query) use ($category_id, $subcategory_id, $brand_id, $search) {
                if (!empty($category_id)) {
                    $query->where('category_id', '=', $category_id);
                }
                if (!empty($subcategory_id)) {
                    $query->where('subcategory_id', '=', $subcategory_id);
                }
                if (!empty($brand_id)) {
                    $query->where('brand_id', '=', $brand_id);
                }
                if (!empty($search)) {
                    $query->where(function ($query) use ($search) {
                        $query->where('product_name', 'LIKE', "%{$search}%")
                            ->Orwhere('description', 'LIKE', "%{$search}%")
                            ->Orwhere('product_code', 'LIKE', "%{$search}%")
                            ->Orwhere('specification', 'LIKE', "%{$search}%")
                            ->Orwhere('part_no', 'LIKE', "%{$search}%")
                            ->Orwhere('product_no', 'LIKE', "%{$search}%")
                            ->orWhereHas('categories', function ($query) use ($search) {
                                $query->where('category_name', 'LIKE', "%{$search}%");
                            })
                            ->orWhereHas('subcategories', function ($query) use ($search) {
                                $query->where('subcategory_name', 'LIKE', "%{$search}%");
                            });
                    });
                }
                $query->where('active', '=', 'Y');
            })
                ->select('id', 'product_name', 'product_code', 'display_name', 'description', 'subcategory_id', 'category_id', 'brand_id', 'product_image', 'unit_id', 'specification', 'part_no', 'product_no', 'model_no', 'suc_del')->latest();

            $db_data = (!empty($pageSize)) ? $query->paginate($pageSize) : $query->get();

            $data = collect([]);
            if ($db_data->isNotEmpty()) {
                foreach ($db_data as $key => $value) {

                    $discount_amount = 0;
                    $total_amount = 0;
                    $ebd_amount = 0;
                    $product_ebd_amount = 0;
                    $ebd_discount = 0;


                    $discount = $value['productpriceinfo']['discount'] ?? 0;
                    $mrp = $value['productpriceinfo']['mrp'] ?? 0;

                    $discount_amount = $mrp * $discount / 100;
                    $total_amount = $mrp - $discount_amount;
                    $total_amount = number_format($total_amount, 2, ".", "");


                    $ebd_discount = $value['getSchemeDetail']['points'] ?? 0;
                    $scheme_type = $value['getSchemeDetail']['orderscheme']['scheme_type'] ?? '';
                    $repetition_type = $value['getSchemeDetail']['orderscheme']['repetition'] ?? '';
                    $is_active = false;
                    if ($repetition_type == '3' || $repetition_type == '4') {
                        $start_date = $value['getSchemeDetail']['orderscheme']['start_date'] ?? '';
                        $end_date = $value['getSchemeDetail']['orderscheme']['end_date'] ?? '';

                        if ($repetition_type == '3') {
                            $startCarbon = Carbon::parse($start_date);
                            $endCarbon = Carbon::parse($end_date);
                            $today = Carbon::today();
                            $startDay = $startCarbon->day;
                            $endDay = $endCarbon->day;
                            $todayDay = $today->day;
                            if ($todayDay >= $startDay && $todayDay <= $endDay) {
                                $is_active = true;
                            }
                        }

                        if ($repetition_type == '4') {
                            $startMonthDay = Carbon::parse($start_date)->format('m-d');
                            $endMonthDay = Carbon::parse($end_date)->format('m-d');
                            $todayMonthDay = Carbon::today()->format('m-d');
                            if (($startMonthDay <= $todayMonthDay && $endMonthDay >= $todayMonthDay) ||
                                ($startMonthDay >= $todayMonthDay && $endMonthDay <= $todayMonthDay)
                            ) {
                                $is_active = true;
                            }
                        }
                    }
                    if ($repetition_type == '2') {
                        $currentDate = Carbon::now();
                        $weekOfMonth = ceil($currentDate->day / 7);
                        $week_repeat = $value['getSchemeDetail']['orderscheme']['week_repeat'] ?? '';
                        if ((int)$week_repeat == (int)$weekOfMonth) {
                            $is_active = true;
                        }
                    }
                    if ($repetition_type == '1') {
                        $day_repeat = explode(',', $value['getSchemeDetail']['orderscheme']['day_repeat']) ?? [];
                        $todayDayOfWeek = Carbon::today()->format('D');
                        if (in_array($todayDayOfWeek, $day_repeat)) {
                            $is_active = true;
                        }
                    }
                    if ($is_active === true) {
                        $scheme_value_type = $value['getSchemeDetail']['orderscheme']['scheme_basedon'] ?? '';

                        $minimum = $value['getSchemeDetail']['orderscheme']['minimum'] ?? 0;
                        $maximum = $value['getSchemeDetail']['orderscheme']['maximum'] ?? 0;

                        if ($scheme_value_type == 'percentage') {
                            $ebd_amount = $total_amount * $ebd_discount / 100;
                            $product_ebd_amount = $total_amount - $ebd_amount;
                        }

                        if ($scheme_value_type == 'value') {

                            $ebd_amount = $ebd_discount;
                            $product_ebd_amount = $total_amount - $ebd_discount;
                        }

                        $ebd_amount = number_format($ebd_amount, 2, ".", "");
                        $product_ebd_amount = number_format($product_ebd_amount, 2, ".", "");
                    } else {
                        $ebd_amount = number_format(0, 2, ".", "");
                        $product_ebd_amount = number_format($total_amount, 2, ".", "");
                    }




                    //$prodcutdetails = $value['prodcutdetails']->where('isprimary',1);
                    $data->push([
                        'id' => isset($value['id']) ? $value['id'] : 0,
                        'product_name' => isset($value['product_name']) ? $value['product_name'].'('.$value['product_code'].')' : '',
                        'display_name' => isset($value['display_name']) ? $value['display_name'] : '',
                        'description' => isset($value['description']) ? $value['description'] : '',
                        'product_image' => isset($value['product_image']) ? $value['product_image'] : '',
                        'subcategory_id' => isset($value['subcategory_id']) ? $value['subcategory_id'] : 0,
                        'subcategory_name' => isset($value['subcategories']['subcategory_name']) ? $value['subcategories']['subcategory_name'] : '',
                        'category_id' => isset($value['category_id']) ? $value['category_id'] : 0,
                        'category_name' => isset($value['categories']['category_name']) ? $value['categories']['category_name'] : '',
                        'brand_id' => isset($value['brand_id']) ? $value['brand_id'] : 0,
                        'brand_name' => isset($value['brands']['brand_name']) ? $value['brands']['brand_name'] : '',
                        'unit_id' => isset($value['unit_id']) ? $value['unit_id'] : 0,
                        'unit_code' => isset($value['unitmeasures']['unit_code']) ? $value['unitmeasures']['unit_code'] : '',
                        'detail_id' => isset($value['productpriceinfo']['id']) ? $value['productpriceinfo']['id'] : 0,
                        'mrp' => isset($value['productpriceinfo']['mrp']) ? $value['productpriceinfo']['mrp'] : 0.00,
                        'price' => isset($value['productpriceinfo']['price']) ? $value['productpriceinfo']['price'] : 0.00,
                        'selling_price' => isset($value['productpriceinfo']['selling_price']) ? $value['productpriceinfo']['selling_price'] : 0.00,
                        'gst' => isset($value['productpriceinfo']['gst']) ? $value['productpriceinfo']['gst'] : 0.00,
                        'specification' => isset($value['suc_del']) ? $value['suc_del'] : '',
                        'part_no' => isset($value['part_no']) ? $value['part_no'] : '',
                        'product_no' => isset($value['product_no']) ? $value['product_no'] : '',
                        'model_no' => isset($value['model_no']) ? $value['model_no'] : '',
                        'hp' => isset($value['specification']) ? $value['specification'] : '',
                        'discount' => $value['productpriceinfo']?$value['productpriceinfo']['discount'] : 0,
                        'amount_diff' => $value['productpriceinfo']['mrp'] - $product_ebd_amount,
                        'ebd_amount' => (string)$ebd_amount,
                        'product_ebd_amount' => (string)$product_ebd_amount,

                    ]);
                }
                return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $data], $this->successStatus);
            }
            return response(['status' => 'error', 'message' => 'No Record Found.', 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function getCategoryData(Request $request)
    {
        try {
            $pageSize = $request->input('pageSize');
            $query = Category::where(function ($query) {
                $query->where('active', '=', 'Y');
            })
                ->select('id', 'category_name', 'category_image', 'ranking')
                ->orderBy('id', 'asc')->latest();
            $db_data = (!empty($pageSize)) ? $query->paginate($pageSize) : $query->get();

            // $querysubcategory = Subcategory::where(function ($query) use ($db_data) {
            //     if (!empty($db_data)) {
            //         $query->where('category_id', '=', $db_data->pluck('id')->first());
            //     }
            //     $query->where('active', '=', 'Y');
            // })
            //     ->select('id', 'subcategory_name', 'subcategory_image', 'category_id', 'ranking')
            //     ->orderBy('ranking', 'asc')
            //     ->latest();
            // $subcategory_data = (!empty($pageSize)) ? $querysubcategory->paginate($pageSize) : $querysubcategory->get();
            // $query_product = Product::with('productdetails', 'productpriceinfo', 'getSchemeDetail')->where(function ($query) use ($subcategory_data) {
            //     if (!empty($subcategory_data)) {
            //         $query->where('subcategory_id', '=', $subcategory_data->pluck('id')->first());
            //     }
            //     $query->where('active', '=', 'Y');
            // })
            //     ->select('id', 'product_name', 'display_name', 'description', 'subcategory_id', 'category_id', 'brand_id', 'product_image', 'unit_id', 'specification', 'part_no', 'product_no', 'model_no', 'suc_del')->latest();

            // $product_data = (!empty($pageSize)) ? $query_product->paginate($pageSize) : $query_product->get();

            $data = collect([]);
            $subcategories = collect([]);
            $products = collect([]);
            if ($db_data->isNotEmpty()) {
                foreach ($db_data as $key => $value) {
                    $data->push([
                        'id' => isset($value['id']) ? $value['id'] : 0,
                        'category_name' => isset($value['category_name']) ? $value['category_name'] : '',
                        'category_image' => isset($value['category_image']) ? $value['category_image'] : '',
                    ]);
                }
                // if ($subcategory_data->isNotEmpty()) {
                //     foreach ($subcategory_data as $key => $rows) {
                //         $subcategories->push([
                //             'id' => isset($rows['id']) ? $rows['id'] : 0,
                //             'subcategory_name' => isset($rows['subcategory_name']) ? $rows['subcategory_name'] : '',
                //             'subcategory_image' => isset($rows['subcategory_image']) ? $rows['subcategory_image'] : '',
                //             'category_id' => isset($rows['category_id']) ? $rows['category_id'] : 0,
                //             'category_name' => isset($rows['categories']['category_name']) ? $rows['categories']['category_name'] : '',
                //         ]);
                //     }
                // }
                // if ($product_data->isNotEmpty()) {

                //     foreach ($product_data as $key => $product) {
                //         //$prodcutdetails = $value['prodcutdetails']->where('isprimary',1);


                //         $discount_amount = 0;
                //         $total_amount = 0;
                //         $ebd_amount = 0;
                //         $product_ebd_amount = 0;
                //         $ebd_discount = 0;


                //         $discount = $product['productpriceinfo']['discount'] ?? 0;
                //         $mrp = $product['productpriceinfo']['mrp'] ?? 0;

                //         $discount_amount = $mrp * $discount / 100;
                //         $total_amount = $mrp - $discount_amount;
                //         $total_amount = number_format($total_amount, 2, ".", "");


                //         $ebd_discount = $product['getSchemeDetail']['points'] ?? 0;
                //         $scheme_type = $product['getSchemeDetail']['orderscheme']['scheme_type'] ?? '';
                //         $scheme_value_type = $product['getSchemeDetail']['orderscheme']['scheme_basedon'] ?? '';

                //         $minimum = $product['getSchemeDetail']['orderscheme']['minimum'] ?? 0;
                //         $maximum = $product['getSchemeDetail']['orderscheme']['maximum'] ?? 0;

                //         if ($scheme_value_type == 'percentage') {
                //             $ebd_amount = $total_amount * $ebd_discount / 100;
                //             $product_ebd_amount = $total_amount - $ebd_amount;
                //         }

                //         if ($scheme_value_type == 'value') {

                //             $ebd_amount = $ebd_discount;
                //             $product_ebd_amount = $total_amount - $ebd_discount;
                //         }

                //         $ebd_amount = number_format($ebd_amount, 2, ".", "");
                //         $product_ebd_amount = number_format($product_ebd_amount, 2, ".", "");



                //         $products->push([
                //             'id' => isset($product['id']) ? $product['id'] : 0,
                //             'product_name' => isset($product['product_name']) ? $product['product_name'] : '',
                //             'display_name' => isset($product['display_name']) ? $product['display_name'] : '',
                //             'description' => isset($product['description']) ? $product['description'] : '',
                //             'product_image' => isset($product['product_image']) ? $product['product_image'] : '',
                //             'subcategory_id' => isset($product['subcategory_id']) ? $product['subcategory_id'] : 0,
                //             'subcategory_name' => isset($product['subcategories']['subcategory_name']) ? $product['subcategories']['subcategory_name'] : '',
                //             'category_id' => isset($product['category_id']) ? $product['category_id'] : 0,
                //             'category_name' => isset($product['categories']['category_name']) ? $product['categories']['category_name'] : '',
                //             'brand_id' => isset($product['brand_id']) ? $product['brand_id'] : 0,
                //             'brand_name' => isset($product['brands']['brand_name']) ? $product['brands']['brand_name'] : '',
                //             'unit_id' => isset($product['unit_id']) ? $product['unit_id'] : 0,
                //             'unit_code' => isset($product['unitmeasures']['unit_code']) ? $product['unitmeasures']['unit_code'] : '',
                //             'detail_id' => isset($product['productpriceinfo']['id']) ? $product['productpriceinfo']['id'] : 0,
                //             'mrp' => isset($product['productpriceinfo']['mrp']) ? $product['productpriceinfo']['mrp'] : 0.00,
                //             'price' => isset($product['productpriceinfo']['price']) ? $product['productpriceinfo']['price'] : 0.00,
                //             'selling_price' => isset($product['productpriceinfo']['selling_price']) ? $product['productpriceinfo']['selling_price'] : 0.00,
                //             'gst' => isset($product['productpriceinfo']['gst']) ? $product['productpriceinfo']['gst'] : 0.00,
                //             'specification' => isset($product['suc_del']) ? $product['suc_del'] : '',
                //             'part_no' => isset($product['part_no']) ? $product['part_no'] : '',
                //             'product_no' => isset($product['product_no']) ? $product['product_no'] : '',
                //             'model_no'  => isset($product['model_no']) ? $product['model_no'] : '',
                //             'hp' => isset($product['specification']) ? $product['specification'] : '',
                //             'ebd_amount' => (string)$ebd_amount,
                //             'product_ebd_amount' => (string)$product_ebd_amount,
                //         ]);
                //     }
                // }
                return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $data, 'subcategories' => $subcategories, 'products' => $products], $this->successStatus);
            }
            return response(['status' => 'error', 'message' => 'No Record Found.', 'data' => $data, 'subcategories' => $subcategories, 'products' => $products], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function getSubCategoryData(Request $request)
    {
        try {
            $pageSize = $request->input('pageSize');
            $category_id = $request->input('category_id');

            $query = Subcategory::where(function ($query) use ($category_id) {
                if (!empty($category_id)) {
                    $query->where('category_id', '=', $category_id);
                }
                $query->where('active', '=', 'Y');
            })
                ->select('id', 'subcategory_name', 'subcategory_image', 'category_id', 'ranking')
                ->orderBy('ranking', 'asc')
                ->latest();
            $db_data = (!empty($pageSize)) ? $query->paginate($pageSize) : $query->get();

            // $query_product = Product::with('productdetails', 'productpriceinfo', 'getSchemeDetail')->where(function ($query) use ($db_data) {
            //     if (!empty($db_data)) {
            //         $query->where('subcategory_id', '=', $db_data->pluck('id')->first());
            //     }
            //     $query->where('active', '=', 'Y');
            // })
            //     ->select('id', 'product_name', 'display_name', 'description', 'subcategory_id', 'category_id', 'brand_id', 'product_image', 'unit_id', 'specification', 'part_no', 'product_no', 'model_no', 'suc_del')->latest();

            // $product_data = (!empty($pageSize)) ? $query_product->paginate($pageSize) : $query_product->get();

            $data = collect([]);
            $products = collect([]);
            if ($db_data->isNotEmpty()) {
                if ($db_data->isNotEmpty()) {
                    foreach ($db_data as $key => $rows) {
                        $data->push([
                            'id' => isset($rows['id']) ? $rows['id'] : 0,
                            'subcategory_name' => isset($rows['subcategory_name']) ? $rows['subcategory_name'] : '',
                            'subcategory_image' => (isset($rows['subcategory_image']) && !empty($rows['subcategory_image']) ) ? url('/public/uploads').'/'.$rows['subcategory_image'] : url('/public/assets/img/placeholder.jpg'),
                            'category_id' => isset($rows['category_id']) ? $rows['category_id'] : 0,
                            'category_name' => isset($rows['categories']['category_name']) ? $rows['categories']['category_name'] : '',
                        ]);
                    }
                }
                // if ($product_data->isNotEmpty()) {

                //     foreach ($product_data as $key => $product) {
                //         //$prodcutdetails = $value['prodcutdetails']->where('isprimary',1);

                //         $discount_amount = 0;
                //         $total_amount = 0;
                //         $ebd_amount = 0;
                //         $product_ebd_amount = 0;
                //         $ebd_discount = 0;


                //         $discount = $product['productpriceinfo']['discount'] ?? 0;
                //         $mrp = $product['productpriceinfo']['mrp'] ?? 0;

                //         $discount_amount = $mrp * $discount / 100;
                //         $total_amount = $mrp - $discount_amount;
                //         $total_amount = number_format($total_amount, 2, ".", "");


                //         $ebd_discount = $product['getSchemeDetail']['points'] ?? 0;
                //         $scheme_type = $product['getSchemeDetail']['orderscheme']['scheme_type'] ?? '';
                //         $scheme_value_type = $product['getSchemeDetail']['orderscheme']['scheme_basedon'] ?? '';

                //         $minimum = $product['getSchemeDetail']['orderscheme']['minimum'] ?? 0;
                //         $maximum = $product['getSchemeDetail']['orderscheme']['maximum'] ?? 0;

                //         if ($scheme_value_type == 'percentage') {
                //             $ebd_amount = $total_amount * $ebd_discount / 100;
                //             $product_ebd_amount = $total_amount - $ebd_amount;
                //         }

                //         if ($scheme_value_type == 'value') {

                //             $ebd_amount = $ebd_discount;
                //             $product_ebd_amount = $total_amount - $ebd_discount;
                //         }

                //         $ebd_amount = number_format($ebd_amount, 2, ".", "");
                //         $product_ebd_amount = number_format($product_ebd_amount, 2, ".", "");




                //         $products->push([
                //             'id' => isset($product['id']) ? $product['id'] : 0,
                //             'product_name' => isset($product['product_name']) ? $product['product_name'] : '',
                //             'display_name' => isset($product['display_name']) ? $product['display_name'] : '',
                //             'description' => isset($product['description']) ? $product['description'] : '',
                //             'product_image' => isset($product['product_image']) ? $product['product_image'] : '',
                //             'subcategory_id' => isset($product['subcategory_id']) ? $product['subcategory_id'] : 0,
                //             'subcategory_name' => isset($product['subcategories']['subcategory_name']) ? $product['subcategories']['subcategory_name'] : '',
                //             'category_id' => isset($product['category_id']) ? $product['category_id'] : 0,
                //             'category_name' => isset($product['categories']['category_name']) ? $product['categories']['category_name'] : '',
                //             'brand_id' => isset($product['brand_id']) ? $product['brand_id'] : 0,
                //             'brand_name' => isset($product['brands']['brand_name']) ? $product['brands']['brand_name'] : '',
                //             'unit_id' => isset($product['unit_id']) ? $product['unit_id'] : 0,
                //             'unit_code' => isset($product['unitmeasures']['unit_code']) ? $product['unitmeasures']['unit_code'] : '',
                //             'detail_id' => isset($product['productpriceinfo']['id']) ? $product['productpriceinfo']['id'] : 0,
                //             'mrp' => isset($product['productpriceinfo']['mrp']) ? $product['productpriceinfo']['mrp'] : 0.00,
                //             'price' => isset($product['productpriceinfo']['price']) ? $product['productpriceinfo']['price'] : 0.00,
                //             'selling_price' => isset($product['productpriceinfo']['selling_price']) ? $product['productpriceinfo']['selling_price'] : 0.00,
                //             'gst' => isset($product['productpriceinfo']['gst']) ? $product['productpriceinfo']['gst'] : 0.00,
                //             'specification' => isset($product['suc_del']) ? $product['suc_del'] : '',
                //             'part_no' => isset($product['part_no']) ? $product['part_no'] : '',
                //             'product_no' => isset($product['product_no']) ? $product['product_no'] : '',
                //             'model_no'  => isset($product['model_no']) ? $product['model_no'] : '',
                //             'hp'  => isset($product['specification']) ? $product['specification'] : '',
                //             'ebd_amount' => (string)$ebd_amount,
                //             'product_ebd_amount' => (string)$product_ebd_amount,
                //         ]);
                //     }
                // }
                return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $data, 'products' => $products], $this->successStatus);
            }
            return response(['status' => 'error', 'message' => 'No Record Found.', 'data' => $data, 'products' => $products], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }


    public function getProductDetails(Request $request)
    {
        try {
            $pageSize = $request->input('pageSize');
            $product_id = $request->input('product_id');
            $query = Product::with('productdetails', 'productpriceinfo', 'getSchemeDetail')->where(function ($query) use ($product_id) {
                $query->where('active', '=', 'Y');
                $query->where('id', '=', $product_id);
            })
            ->first();

            $detail = collect([]);
            $data = collect([]);
            if (!empty($query)) {
                foreach ($query['productdetails'] as $key => $value) {
                    $detail->push([
                        'detail_id' => isset($value['id']) ? $value['id'] : 0,
                        'detail_title' => isset($value['detail_title']) ? $value['detail_title'] : '',
                        'detail_description' => isset($value['detail_description']) ? $value['detail_description'] : '',
                        'mrp' => isset($value['mrp']) ? $value['mrp'] : 0.00,
                        'price' => isset($value['price']) ? $value['price'] : 0.00,
                        'min_price' => isset($value['price']) ? $value['price'] - 10 : 0.00,
                        'max_price' => isset($value['price']) ? $value['price'] + 10 : 0.00,
                        'selling_price' => isset($value['selling_price']) ? $value['selling_price'] : 0.00,
                        'gst' => isset($value['gst']) ? $value['gst'] : 0,
                        'discount' => isset($value['discount']) ? $value['discount'] : 0,
                        'max_discount' => isset($value['max_discount']) ? $value['max_discount'] : 0,
                    ]);
                }
                //$primary = $query['productdetails']->where('isprimary',1)->first();
                $primary = $query['productpriceinfo'];


                $discount_amount = 0;
                $total_amount = 0;
                $ebd_amount = 0;
                $product_ebd_amount = 0;
                $ebd_discount = 0;


                $discount = $query['productpriceinfo']['discount'] ?? 0;
                $mrp = $query['productpriceinfo']['mrp'] ?? 0;

                $discount_amount = $mrp * $discount / 100;
                $total_amount = $mrp - $discount_amount;
                $total_amount = number_format($total_amount, 2, ".", "");


                $ebd_discount = $value['getSchemeDetail']['points'] ?? 0;
                $scheme_type = $value['getSchemeDetail']['orderscheme']['scheme_type'] ?? '';
                $repetition_type = $value['getSchemeDetail']['orderscheme']['repetition'] ?? '';
                $is_active = false;
                if ($repetition_type == '3' || $repetition_type == '4') {
                    $start_date = $value['getSchemeDetail']['orderscheme']['start_date'] ?? '';
                    $end_date = $value['getSchemeDetail']['orderscheme']['end_date'] ?? '';

                    if ($repetition_type == '3') {
                        $startCarbon = Carbon::parse($start_date);
                        $endCarbon = Carbon::parse($end_date);
                        $today = Carbon::today();
                        $startDay = $startCarbon->day;
                        $endDay = $endCarbon->day;
                        $todayDay = $today->day;
                        if ($todayDay >= $startDay && $todayDay <= $endDay) {
                            $is_active = true;
                        }
                    }

                    if ($repetition_type == '4') {
                        $startMonthDay = Carbon::parse($start_date)->format('m-d');
                        $endMonthDay = Carbon::parse($end_date)->format('m-d');
                        $todayMonthDay = Carbon::today()->format('m-d');
                        if (($startMonthDay <= $todayMonthDay && $endMonthDay >= $todayMonthDay) ||
                            ($startMonthDay >= $todayMonthDay && $endMonthDay <= $todayMonthDay)
                        ) {
                            $is_active = true;
                        }
                    }
                }
                if ($repetition_type == '2') {
                    $currentDate = Carbon::now();
                    $weekOfMonth = ceil($currentDate->day / 7);
                    $week_repeat = $value['getSchemeDetail']['orderscheme']['week_repeat'] ?? '';
                    if ((int)$week_repeat == (int)$weekOfMonth) {
                        $is_active = true;
                    }
                }
                if ($repetition_type == '1') {
                    $day_repeat = explode(',', $value['getSchemeDetail']['orderscheme']['day_repeat']) ?? [];
                    $todayDayOfWeek = Carbon::today()->format('D');
                    if (in_array($todayDayOfWeek, $day_repeat)) {
                        $is_active = true;
                    }
                }
                if ($is_active === true) {
                    $scheme_value_type = $value['getSchemeDetail']['orderscheme']['scheme_basedon'] ?? '';

                    $minimum = $value['getSchemeDetail']['orderscheme']['minimum'] ?? 0;
                    $maximum = $value['getSchemeDetail']['orderscheme']['maximum'] ?? 0;

                    if ($scheme_value_type == 'percentage') {
                        $ebd_amount = $total_amount * $ebd_discount / 100;
                        $product_ebd_amount = $total_amount - $ebd_amount;
                    }

                    if ($scheme_value_type == 'value') {

                        $ebd_amount = $ebd_discount;
                        $product_ebd_amount = $total_amount - $ebd_discount;
                    }

                    $ebd_amount = number_format($ebd_amount, 2, ".", "");
                    $product_ebd_amount = number_format($product_ebd_amount, 2, ".", "");
                } else {
                    $ebd_amount = number_format(0, 2, ".", "");
                    $product_ebd_amount = number_format($total_amount, 2, ".", "");
                }


                $data = collect([
                    'id' => isset($query['id']) ? $query['id'] : 0,
                    'product_name' => isset($query['product_name']) ? $query['product_name'] : '',
                    'display_name' => isset($query['display_name']) ? $query['display_name'] : '',
                    'description' => isset($query['description']) ? $query['description'] : '',
                    'product_image' => (isset($query['product_image']) && !empty($query['product_image'])) ? url('/public/uploads').'/'.$query['product_image'] : url('/public/assets/img/placeholder.jpg'),
                    'subcategory_id' => isset($query['subcategory_id']) ? $query['subcategory_id'] : 0,
                    'subcategory_name' => isset($query['subcategories']['subcategory_name']) ? $query['subcategories']['subcategory_name'] : '',
                    'category_id' => isset($query['category_id']) ? $query['category_id'] : 0,
                    'category_name' => isset($query['categories']['category_name']) ? $query['categories']['category_name'] : '',
                    'brand_id' => isset($query['brand_id']) ? $query['brand_id'] : 0,
                    'brand_name' => isset($query['brands']['brand_name']) ? $query['brands']['brand_name'] : '',
                    'unit_id' => isset($query['unit_id']) ? $query['unit_id'] : 0,
                    'unit_code' => isset($query['unitmeasures']['unit_code']) ? $query['unitmeasures']['unit_code'] : '',
                    'detail_id' => isset($primary['id']) ? $primary['id'] : 0,
                    'mrp' => isset($primary['mrp']) ? $primary['mrp'] : 0.00,
                    'price' => isset($primary['price']) ? $primary['price'] : 0.00,
                    'min_price' => isset($primary['price']) ? $primary['price'] - 10 : 0.00,
                    'max_price' => isset($primary['price']) ? $primary['price'] + 10 : 0.00,
                    'selling_price' => isset($primary['selling_price']) ? $primary['selling_price'] : 0.00,
                    'gst' => isset($primary['gst']) ? $primary['gst'] : 0,
                    'discount' => isset($primary['discount']) ? $primary['discount'] : 0,
                    'max_discount' => isset($primary['max_discount']) ? $primary['max_discount'] : 0,
                    'specification' => isset($query['suc_del']) ? $query['suc_del'] : '',
                    'part_no' => isset($query['part_no']) ? $query['part_no'] : '',
                    'product_no' => isset($query['product_no']) ? $query['product_no'] : '',
                    'model_no' => isset($query['model_no']) ? $query['model_no'] : '',
                    'hp' => isset($query['specification']) ? $query['specification'] : '',
                    'stage' => isset($query['product_no']) ? $query['product_no'] : '',
                    'expiry_interval' => isset($query['expiry_interval']) ? $query['expiry_interval'] : '',
                    'expiry_interval_preiod' => isset($query['expiry_interval_preiod']) ? $query['expiry_interval_preiod'] : '',
                    'phase' => isset($query['phase']) ? $query['phase'] : '',
                    'ebd_amount' => (string)$ebd_amount,
                    'scheme_amount' => (string)$ebd_amount,
                    'amount_diff' => $primary['mrp'] - $product_ebd_amount,
                    'product_ebd_amount' => (string)$product_ebd_amount,
                    'details' => $detail
                ]);
                return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $data], $this->successStatus);
            }
            return response(['status' => 'error', 'message' => 'No Record Found.', 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function getGiftList(Request $request)
    {
        try {
            $pageSize = $request->input('pageSize');
            $category_id = $request->input('category_id');
            $subcategory_id = $request->input('subcategory_id');
            $brand_id = $request->input('brand_id');
            $search = $request->input('search');
            $query = Gift::where(function ($query) use ($category_id, $subcategory_id, $brand_id, $search) {
                if (!empty($category_id)) {
                    $query->where('category_id', '=', $category_id);
                }
                if (!empty($subcategory_id)) {
                    $query->where('subcategory_id', '=', $subcategory_id);
                }
                if (!empty($brand_id)) {
                    $query->where('brand_id', '=', $brand_id);
                }
                if (!empty($search)) {
                    $query->orWhere('product_name', 'LIKE', "%{$search}%");
                    $query->orWhere('display_name', 'LIKE', "%{$search}%");
                    $query->orWhere('description', 'LIKE', "%{$search}%");
                    $query->orWhere('product_name', 'LIKE', "%{$search}%");
                    $query->orWhereHas('categories', function ($query) use ($search) {
                        $query->where('category_name', 'LIKE', "%{$search}%");
                    });
                    $query->orWhereHas('subcategories', function ($query) use ($search) {
                        $query->where('subcategory_name', 'LIKE', "%{$search}%");
                    });
                }
                $query->where('active', '=', 'Y');
            })
                ->select('id', 'product_name', 'display_name', 'description', 'product_image', 'mrp', 'price', 'points', 'subcategory_id', 'category_id', 'brand_id', 'unit_id')->latest();
            $db_data = (!empty($pageSize)) ? $query->paginate($pageSize) : $query->get();

            $data = collect([]);
            if ($db_data->isNotEmpty()) {
                foreach ($db_data as $key => $value) {
                    //$prodcutdetails = $value['prodcutdetails']->where('isprimary',1);
                    $data->push([
                        'id' => isset($value['id']) ? $value['id'] : 0,
                        'product_name' => isset($value['product_name']) ? $value['product_name'] : '',
                        'display_name' => isset($value['display_name']) ? $value['display_name'] : '',
                        'description' => isset($value['description']) ? $value['description'] : '',
                        'product_image' => isset($value['product_image']) ? $value['product_image'] : '',
                        'subcategory_id' => isset($value['subcategory_id']) ? $value['subcategory_id'] : 0,
                        'subcategory_name' => isset($value['subcategories']['subcategory_name']) ? $value['subcategories']['subcategory_name'] : '',
                        'category_id' => isset($value['category_id']) ? $value['category_id'] : 0,
                        'category_name' => isset($value['categories']['category_name']) ? $value['categories']['category_name'] : '',
                        'brand_id' => isset($value['brand_id']) ? $value['brand_id'] : 0,
                        'brand_name' => isset($value['brands']['brand_name']) ? $value['brands']['brand_name'] : '',
                        'unit_id' => isset($value['unit_id']) ? $value['unit_id'] : 0,
                        'unit_code' => isset($value['unitmeasures']['unit_code']) ? $value['unitmeasures']['unit_code'] : '',
                        'mrp' => isset($value['mrp']) ? $value['mrp'] : 0.00,
                        'price' => isset($value['price']) ? $value['price'] : 0.00,
                        'points' => isset($value['points']) ? $value['points'] : 0,

                    ]);
                }
                return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $data], $this->successStatus);
            }
            return response(['status' => 'error', 'message' => 'No Record Found.', 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }
}
