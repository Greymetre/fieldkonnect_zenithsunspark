<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use DataTables;
use Validator;
use Gate;
use App\Models\{Pincode, City, District, State, Country, Customers, Category, Product, Address, Attachment, Attendance, Branch, BranchStock, Order, Status, Settings, Tasks, ProductDetails, Sales, UserReporting, CheckIn, Complaint, ComplaintTimeline, ComplaintWorkDone, CompOffLeave, CustomerDetails, CustomerOutstanting, DealerAppointment, DealerAppointmentKyc, EmployeeDetail, EndUser, Expenses, GiftModel, GiftSubcategory, Marketing, MspActivity, Notes, OrderSchemeDetail, ParentDetail, PrimarySales, PrimaryScheme, Redemption, SalesTargetUsers, SchemeDetails, ServiceBill, ServiceChargeCategories, ServiceChargeProducts, Services, Subcategory, TourProgramme, TransactionHistory, User, UserCityAssign, WarrantyActivation, ServiceChargeChargeType, OrderDetails, ServiceComplaintReason, ServiceBillComplaintType, ServiceGroupComplaint, OpeningStock, BranchOprningQuantity, Invoice, PlannedSOP};
use App\Models\UserLiveLocation;
use App\Models\UserActivity;
use App\Http\Controllers\SendNotifications;
use Carbon\Carbon;
use LDAP\Result;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Crypt;
use App\Models\MarketingActivity;


class AjaxController extends Controller
{


    public function getState(Request $request)
    {
        try {
            $country = $request->input('country_id');
            $states = State::where(function ($query) use ($country) {
                if (isset($country)) {
                    $query->where('country_id', '=', $country);
                }
                $query->where('active', '=', 'Y');
            })
                ->select('id', 'state_name')->orderBy('state_name', 'asc')->get();
            return response()->json($states);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getDistrict(Request $request)
    {
        try {
            $state = $request->input('state_id');
            $district = District::where(function ($query) use ($state) {
                if (isset($state)) {
                    $query->where('state_id', '=', $state);
                }
                $query->where('active', '=', 'Y');
            })
                ->select('id', 'district_name')->orderBy('district_name', 'asc')->get();
            return response()->json($district);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getCity(Request $request)
    {
        try {
            $district = $request->input('district_id');
            if (is_array($district)) {
                $cities = City::where(function ($query) use ($district) {
                    if (isset($district) && count($district) > 0) {
                        $query->whereIn('district_id', $district);
                    }
                    $query->where('active', '=', 'Y');
                })
                    ->select('id', 'city_name')->orderBy('city_name', 'asc')->get();
            } else {
                $cities = City::where(function ($query) use ($district) {
                    if (isset($district)) {
                        $query->where('district_id', '=', $district);
                    }
                    $query->where('active', '=', 'Y');
                })
                    ->select('id', 'city_name')->orderBy('city_name', 'asc')->get();
            }
            return response()->json($cities);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getPincode(Request $request)
    {
        try {
            $city = $request->input('city_id');
            $cities = Pincode::where(function ($query) use ($city) {
                if (isset($city)) {
                    $query->where('city_id', '=', $city);
                }
                $query->where('active', '=', 'Y');
            })
                ->select('id', 'pincode')->orderBy('pincode', 'asc')->get();
            return response()->json($cities);
        } catch (\Exception $e) {
            return $e;
        }
    }


    public function getAddressData(Request $request)
    {
        try {
            $pincode = $request->input('pincode_id');
            $data = Pincode::with('cityname', 'cityname.districtname', 'cityname.districtname.statename', 'cityname.districtname.statename.countryname')->where('id', '=', $pincode)->select('id', 'city_id')->first();
            $address = collect([
                'city_id' => isset($data['city_id']) ? $data['city_id'] : '',
                'city_name' => isset($data['cityname']['city_name']) ? $data['cityname']['city_name'] : '',
                'district_id' => isset($data['cityname']['district_id']) ? $data['cityname']['district_id'] : '',
                'district_name' => isset($data['cityname']['districtname']['district_name']) ? $data['cityname']['districtname']['district_name'] : '',
                'state_id' => isset($data['cityname']['districtname']['state_id']) ? $data['cityname']['districtname']['state_id'] : '',
                'state_name' => isset($data['cityname']['districtname']['statename']['state_name']) ? $data['cityname']['districtname']['statename']['state_name'] : '',
                'country_id' => isset($data['cityname']['districtname']['statename']['country_id']) ? $data['cityname']['districtname']['statename']['country_id'] : '',
                'country_name' => isset($data['cityname']['districtname']['statename']['countryname']['country_name']) ? $data['cityname']['districtname']['statename']['countryname']['country_name'] : '',
            ]);
            return response()->json($address);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getAddressInfo(Request $request)
    {
        try {
            $address_id = $request->input('address_id');
            $data = Address::with('cityname', 'districtname', 'statename', 'countryname', 'pincodename')->where('id', '=', $address_id)->select('id', 'address1', 'address2', 'landmark', 'locality', 'country_id', 'state_id', 'district_id', 'city_id', 'pincode_id')->first();
            $address = $data['address1'] . ' ' . $data['address2'] . ' ' . $data['landmark'] . ' ' . $data['locality'] . ' ' . $data['cityname']['city_name'] . ' ' . $data['districtname']['district_name'] . ' ' . $data['statename']['state_name'] . ' ' . $data['countryname']['country_name'] . $data['pincodename']['pincode'];
            return response()->json($address);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getUserInfo(Request $request)
    {
        try {
            $user_id = $request->input('user_id');
            $data = User::with('reportinginfo', 'getdesignation', 'userinfo')->where('id', '=', $user_id)
                ->select('id', 'name', 'mobile', 'designation_id', 'reportingid', 'employee_codes', 'branch_id')
                ->first();

            return response()->json($data);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getCustomerData(Request $request)
    {
        try {
            $customer_id = $request->input('customer_id');
            $data = Customers::with('customeraddress', 'addresslists')
                ->where('id', '=', $customer_id)
                ->first();
            $addresslists = collect([]);
            if ($data['addresslists']) {
                foreach ($data['addresslists'] as $key => $rows) {
                    $addresslists->push([
                        'address_id' => $rows['id'],
                        'address1' => $rows['address1'],
                        'address2' => $rows['address2'],
                        'landmark' => $rows['landmark'],
                        'locality' => $rows['locality'],
                    ]);
                }
            }
            $customer = collect([
                'name' => isset($data['name']) ? $data['name'] : '',
                'first_name' => isset($data['first_name']) ? $data['first_name'] : '',
                'last_name' => isset($data['last_name']) ? $data['last_name'] : '',
                'mobile' => isset($data['mobile']) ? $data['mobile'] : '',
                'customertype' => isset($data['customertype']) ? $data['customertype'] : '',
                'email' => isset($data['email']) ? $data['email'] : '',
                'created_by' => isset($data['created_by']) ? $data['created_by'] : '',
                'executive_id' => isset($data['executive_id']) ? $data['executive_id'] : '',
                'address1' => isset($data['customeraddress']['address1']) ? $data['customeraddress']['address1'] . ' ' . $data['customeraddress']['address2'] . ' ' . $data['customeraddress']['landmark'] . ' ' . $data['customeraddress']['locality'] : '',
                'address2' => isset($data['customeraddress']['cityname']['city_name']) ? $data['customeraddress']['cityname']['city_name'] . ', ' . $data['customeraddress']['pincodename']['pincode'] : '',
                'addresslists' => $addresslists
            ]);
            return response()->json($customer);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getCategoryData(Request $request)
    {
        try {
            $data = Category::where(function ($query) {
                $query->where('active', '=', 'Y');
            })
                ->select('id', 'category_name', 'category_image')
                ->orderBy('category_name', 'asc')
                ->get();
            return response()->json($data);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getPrimaryGroup(Request $request)
    {
        try {
            $data = PrimarySales::whereNotNull($request->group_type)->groupBy($request->gruop_type)->pluck($request->gruop_type);
            return response()->json($data);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getSubCategoryData(Request $request)
    {
        try {
            $data = Subcategory::where(function ($query) {
                $query->where('active', '=', 'Y');
            });
            if ($request->cat_id && $request->cat_id != null && $request->cat_id != '') {
                $data->where('category_id', $request->cat_id);
            }
            $data = $data->select('id', 'subcategory_name')
                ->orderBy('subcategory_name', 'asc')
                ->get();
            return response()->json($data);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getProductData(Request $request)
    {
        try {
            $branchId = $request->branch_id;
            $sub_category = $request->sub_cat;
            $category = $request->category;
            $data = Product::where(function ($query) use ($sub_category, $category) {
                if ($sub_category && $sub_category != null) {
                    $query->where('subcategory_id', $sub_category);
                }
                if ($category && $category != null) {
                    $query->where('category_id', $category);
                }
                $query->where('active', '=', 'Y');
            })
                ->select('id', 'product_name', 'product_image', 'display_name', 'product_code')
                ->orderBy('product_name', 'asc')
                ->get();
            return response()->json($data);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getSubCategory(Request $request)
    {
        try {
            $branchId = $request->branch_id; // Example: 2
            $subcategory_ids = Product::whereRaw("FIND_IN_SET(?, branch_id)", [$branchId])->where('category_id', $request->category)
                ->distinct()
                ->pluck('subcategory_id');
            $sub_categories = Subcategory::whereIn('id', $subcategory_ids)->get();
            return response()->json($sub_categories);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getProductInfoListBySubcategory(Request $request)
    {
        try {
            $branchId = $request->branch_id; // Example: 2
            $sub_category = $request->product_subcategory ?? '';
            $product = Product::whereRaw("FIND_IN_SET(?, branch_id)", [$branchId])->where('subcategory_id', $sub_category)
                ->get();
            return response()->json($product);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getFullDetailsOfProduct(Request $request)
    {
        try {
            $planning_month = '';
            if (isset($request->date)) {
                $formatted_date = Carbon::createFromFormat('F Y', $request->date)->subMonth()->startOfMonth();
                $planning_month = $formatted_date->format("Y-m-d");
            }
            $dateParts = explode(' ', $request->date); // Example: ['April', '2025']
            $month = $dateParts[0];
            $year = (int) $dateParts[1];

            // Determine previous financial year
            if (in_array($month, ['January', 'February', 'March'])) {
                $startYear = $year - 2;
                $endYear = $year - 1;
            } else {
                $startYear = $year - 1;
                $endYear = $year;
            }
            $product = Product::with('productdetails', 'categories', 'subcategories')->find($request->product_id);
            $opening_stock = OpeningStock::orWhere(['item_code' => $product->product_code, 'item_code' => $product->sap_code])->where(['item_group' => $product->subcategories->subcategory_name])->whereRaw("FIND_IN_SET(?, branch_id)", [$request->branch_id])->first();

            $branchOprningQuantity = PlannedSOP::where('product_id', $request->product_id)
                ->where("branch_id", $request->branch_id)
                ->whereDate('planning_month', $planning_month)
                ->first();
            $months = [];
            for ($m = 4; $m <= 12; $m++) {
                $months["$startYear-" . str_pad($m, 2, '0', STR_PAD_LEFT)] = 0;
            }
            for ($m = 1; $m <= 3; $m++) {
                $months["$endYear-" . str_pad($m, 2, '0', STR_PAD_LEFT)] = 0;
            }
            // Get primary sales data for the previous financial year
            $salesData = PrimarySales::where(['product_id' => $product->id, 'branch_id' => $request->branch_id])
                ->whereBetween('invoice_date', ["$startYear-04-01", "$endYear-03-31"])
                ->selectRaw('DATE_FORMAT(invoice_date, "%Y-%m") as month, SUM(quantity) as total_qty')
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total_qty', 'month')
                ->toArray();

            $planningDate = Carbon::parse($planning_month);
            $threeMonthsStart = $planningDate->copy()->subMonths(2)->startOfMonth();
            $threeMonthsEnd = $planningDate->copy()->endOfMonth();
            $twelveMonthsStart = $planningDate->copy()->subMonths(11)->startOfMonth();
            $twelveMonthsEnd = $planningDate->copy()->endOfMonth();

            $threeMonthAvg = PrimarySales::where(['product_id' => $product->id, 'branch_id' => $request->branch_id])
                ->whereBetween('invoice_date', [$threeMonthsStart->toDateString(), $threeMonthsEnd->toDateString()])
                ->sum('quantity');

            $twelveMonthAvg = PrimarySales::where(['product_id' => $product->id, 'branch_id' => $request->branch_id])
                ->whereBetween('invoice_date', [$twelveMonthsStart->toDateString(), $twelveMonthsEnd->toDateString()])
                ->sum('quantity');

            $threeMonthAvg = (int)$threeMonthAvg / 3;
            $twelveMonthAvg = (int)$twelveMonthAvg / 12;

            // Merge sales data into the initialized months array
            $salesByMonth = array_merge($months, $salesData);

            $lastYearSameMonthKey = Carbon::parse($planning_month)->subYear()->addMonth()->format('Y-m');
            $sameMonthLastYearSales = $salesByMonth[$lastYearSameMonthKey] ?? 0;
            $forecast_reccomendation = max($sameMonthLastYearSales, $threeMonthAvg, $twelveMonthAvg);


            return response()->json([
                'product' => $product,
                'sales_by_month' => $salesByMonth,
                'opening_stock'  => $opening_stock,
                'branchOprningQuantity' => $branchOprningQuantity ?? Null,
                'threeMonthAvg' => $threeMonthAvg,
                'twelveMonthAvg' => $twelveMonthAvg,
                'forecast_reccomendation' => $forecast_reccomendation,
                'sameMonthLastYearSales' => $sameMonthLastYearSales
            ]);
        } catch (\Exception $e) {
            return $e;
        }
    }


    public function getUserList(Request $request)
    {
        try {
            session()->forget('executive_id');
            $beat_id = $request->input('beat_id');
            $payroll = $request->input('payroll');
            $branch_id = $request->input('branch_id');
            $division_id = $request->input('division_id');
            $roles = $request->input('roles');
            $userids = getUsersReportingToAuth();
            $login_userid = Auth::user()->id;
            $all_users = User::all();
            $userinfo = User::where('id', '=', $login_userid)->first();
            $data = User::whereDoesntHave('roles', function ($query) {
                $query->where('id', 29);
            })->where(function ($query) use ($beat_id, $userids, $payroll, $userinfo, $branch_id, $division_id, $roles) {
                if (isset($beat_id)) {
                    $query->whereHas('userbeats', function ($query) use ($beat_id) {
                        $query->where('beat_id', '=', $beat_id);
                    });
                }
                if (isset($roles) && count($roles) > 0) {
                    // dd($roles);
                    $query->whereHas('roles', function ($query) use ($roles) {
                        $query->whereIn('id', $roles);
                    });
                }
                if (isset($payroll)) {
                    $query->where('payroll', '=', $payroll);
                }
                if (isset($branch_id)) {
                    $query->where('branch_id', '=', $branch_id);
                }
                if (isset($division_id)) {
                    $query->where('division_id', '=', $division_id);
                }
                if (!$userinfo->hasRole('superadmin') && !$userinfo->hasRole('Admin') && !$userinfo->hasRole('Sub_Admin') && !$userinfo->hasRole('HR_Admin') && !$userinfo->hasRole('HO_Account')  && !$userinfo->hasRole('Sub_Support') && !$userinfo->hasRole('Accounts Order') && !$userinfo->hasRole('Service Admin') && !$userinfo->hasRole('All Customers')) {
                    $query->whereIn('id', $userids);
                }
            })
                ->select('id', 'name', 'mobile', 'first_name', 'last_name', 'employee_codes')
                ->orderBy('name', 'asc')
                ->get();
            return response()->json($data);
        } catch (\Exception $e) {
            return $e;
        }
    }
    public function getUserListAppoint(Request $request)
    {
        try {
            $branch_id = $request->input('branch_id');
            $data = User::where(function ($query) use ($branch_id) {

                if (isset($branch_id)) {
                    $query->whereRaw("FIND_IN_SET(?, branch_id)", [$branch_id]);
                }
                $query->where('active', '=', 'Y');
            })
                ->select('id', 'name', 'mobile', 'first_name', 'last_name', 'employee_codes')
                ->orderBy('name', 'asc')
                ->get();
            return response()->json($data);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getRetailerlist(Request $request)
    {
        try {
            $state = $request->input('state_id');
            $district = $request->input('district_id');
            $city = $request->input('city_id');
            $users = $request->input('user_id');
            $data = Customers::where(function ($query) use ($users) {
                // if (isset($users)) {
                //     $query->whereIn('executive_id', $users);
                // }
                $query->where('active', '=', 'Y');
            })
                ->whereHas('customeraddress', function ($query) use ($state, $district, $city) {
                    if (isset($state)) {
                        $query->where('state_id', '=', $state);
                    }
                    if (is_array($district)) {
                        if (count($district) > 0) {
                            $query->whereIn('district_id', $district);
                        }
                    } else {
                        if (isset($district)) {
                            $query->where('district_id', '=', $district);
                        }
                    }
                    if (is_array($city)) {
                        if (count($city) > 0) {
                            $query->whereIn('city_id', $city);
                        }
                    } else {
                        if (isset($city)) {
                            $query->where('city_id', '=', $city);
                        }
                    }
                })
                ->select('id', 'name', 'mobile', 'first_name', 'last_name')
                ->orderBy('name', 'asc')
                ->get();
            return response()->json($data);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getProductInfo(Request $request)
    {
        try {
            $product_id = $request->input('product_id');

            $data = Product::with('productdetails', 'categories')
                ->where(function ($query) use ($product_id) {
                    if (isset($product_id)) {
                        $query->where('id', '=', $product_id);
                    }
                    $query->where('active', '=', 'Y');
                })
                ->orderBy('product_name', 'asc')
                ->first();
            $opening_stock = OpeningStock::orWhere(['item_code' => $data->product_code, 'item_code' => $data->sap_code])->where(['item_group' => $data->subcategories->subcategory_name])->whereRaw("FIND_IN_SET(?, branch_id)", [$request->branch_id])->first();
            $product = collect([
                'id' => isset($data['id']) ? $data['id'] : '',
                'product_name' => isset($data['product_name']) ? $data['product_name'] : '',
                'product_description' => isset($data['description']) ? $data['description'] : '',
                'product_code' => isset($data['product_code']) ? $data['product_code'] : '',
                'specification' => isset($data['specification']) ? $data['specification'] : '',
                'product_no' => isset($data['product_no']) ? $data['product_no'] : '',
                'phase' => isset($data['phase']) ? $data['phase'] : '',
                'product_image' => isset($data['product_image']) ? $data['product_image'] : '',
                'display_name' => isset($data['display_name']) ? $data['display_name'] : '',
                'mrp' => isset($data['productdetails'][0]['mrp']) ? $data['productdetails'][0]['mrp'] : '',
                'price' => isset($data['productdetails'][0]['price']) ? $data['productdetails'][0]['price'] : '',
                'selling_price' => isset($data['productdetails'][0]['selling_price']) ? $data['productdetails'][0]['selling_price'] : '',
                'gst' => isset($data['productdetails'][0]['gst']) ? $data['productdetails'][0]['gst'] : '',
                'discount' => isset($data['productdetails'][0]['discount']) ? $data['productdetails'][0]['discount'] : '',
                'max_discount' => ($data['productdetails'][0]['max_discount']) ? $data['productdetails'][0]['max_discount'] : 0.00,
                'budget_for_month' => ($data['productdetails'][0]['budget_for_month']) ? $data['productdetails'][0]['budget_for_month'] : '',
                'top_sku' => ($data['productdetails'][0]['top_sku']) ? $data['productdetails'][0]['top_sku'] : '',
                'scheme_discount' => $data['getSchemeDetail']['points'] ?? 0.00,
                'repetition' => $data['getSchemeDetail']['orderscheme']['repetition'] ?? '',
                'scheme_name' => $data['getSchemeDetail']['orderscheme']['scheme_name'] ?? '',
                'scheme_type' => $data['getSchemeDetail']['orderscheme']['scheme_type'] ?? '',
                'scheme_value_type' => $data['getSchemeDetail']['orderscheme']['scheme_basedon'] ?? '',
                'minimum' => $data['getSchemeDetail']['orderscheme']['minimum'] ?? 0,
                'maximum' => $data['getSchemeDetail']['orderscheme']['maximum'] ?? 0,
                'start_date' => $data['getSchemeDetail']['orderscheme']['start_date'] ?? 0,
                'hsn_sac' => $data['hsn_sac'] ?? NULL,
                'hsn_sac_no' => $data['hsn_sac_no'] ?? NULL,


                'productdetails' => $data['productdetails'],
                'categories' => $data['categories'],
                'subcategories' => $data['subcategories'],
                'opening_stock' => $opening_stock ? $opening_stock->opening_stocks : 0,
            ]);


            if ($product['repetition'] == '3' || $product['repetition'] == '4') {
                $start_date = $data['getSchemeDetail']['orderscheme']['start_date'] ?? '';
                $end_date = $data['getSchemeDetail']['orderscheme']['end_date'] ?? '';

                if ($product['repetition'] == '3') {
                    $startCarbon = Carbon::parse($start_date);
                    $endCarbon = Carbon::parse($end_date);
                    $today = Carbon::today();
                    $startDay = $startCarbon->day;
                    $endDay = $endCarbon->day;
                    $todayDay = $today->day;
                    if ($todayDay >= $startDay && $todayDay <= $endDay) {
                    } else {
                        $product['scheme_discount'] = 0.00;
                    }
                }

                if ($product['repetition'] == '4') {
                    $startMonthDay = Carbon::parse($start_date)->format('m-d');
                    $endMonthDay = Carbon::parse($end_date)->format('m-d');
                    $todayMonthDay = Carbon::today()->format('m-d');
                    if (($startMonthDay <= $todayMonthDay && $endMonthDay >= $todayMonthDay) ||
                        ($startMonthDay >= $todayMonthDay && $endMonthDay <= $todayMonthDay)
                    ) {
                    } else {
                        $product['scheme_discount'] = 0.00;
                    }
                }
            }
            if ($product['repetition'] == '2') {
                $currentDate = Carbon::now();
                $weekOfMonth = ceil($currentDate->day / 7);
                $week_repeat = $data['getSchemeDetail']['orderscheme']['week_repeat'] ?? '';
                if ((int)$week_repeat == (int)$weekOfMonth) {
                } else {
                    $product['scheme_discount'] = 0.00;
                }
            }
            if ($product['repetition'] == '1') {
                $day_repeat = explode(',', $data['getSchemeDetail']['orderscheme']['day_repeat']) ?? [];
                $todayDayOfWeek = Carbon::today()->format('D');
                if (in_array($todayDayOfWeek, $day_repeat)) {
                } else {
                    $product['scheme_discount'] = 0.00;
                }
            }

            $warrenty_time = $data->expiry_interval_preiod ?? 0;;
            if ($data->expiry_interval == "Month") {
                $warrenty_time = $data->expiry_interval_preiod;
            } else if ($data->expiry_interval == "Day") {
                $warrenty_time = round($data->expiry_interval_preiod / 30);
            } else if ($data->expiry_interval == "Year") {
                $warrenty_time = round($data->expiry_interval_preiod * 12);
            }
            $product['warrenty_time'] = $warrenty_time;
            return response()->json($product);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getProductDetailInfo(Request $request)
    {
        try {
            $productdetail_id = $request->input('productdetail_id');
            $data = ProductDetails::where('id', '=', $productdetail_id)
                ->select('id', 'detail_title', 'mrp', 'price', 'discount', 'max_discount', 'selling_price', 'gst')
                ->first();
            return response()->json($data);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getOrderInfo(Request $request)
    {
        try {
            $order_id = $request->input('order_id');
            $data = Order::with('buyers', 'sellers', 'orderdetails')->where('id', '=', $order_id)->select('id', 'buyer_id', 'seller_id', 'orderno', 'order_date', 'total_gst', 'total_discount', 'extra_discount', 'extra_discount_amount', 'sub_total', 'grand_total', 'address_id')->first();
            $order = collect([
                'buyer_id' => isset($data['buyer_id']) ? $data['buyer_id'] : '',
                'buyer_name' => isset($data['buyers']['name']) ? $data['buyers']['name'] : '',
                'seller_id' => isset($data['seller_id']) ? $data['seller_id'] : '',
                'seller_name' => isset($data['sellers']['name']) ? $data['sellers']['name'] : '',
                'orderno' => isset($data['orderno']) ? $data['orderno'] : '',
                'order_date' => isset($data['order_date']) ? $data['order_date'] : '',
                'total_gst' => isset($data['total_gst']) ? $data['total_gst'] : '',
                'total_discount' => isset($data['total_discount']) ? $data['total_discount'] : '',
                'extra_discount' => isset($data['extra_discount']) ? $data['extra_discount'] : '',
                'extra_discount_amount' => isset($data['extra_discount_amount']) ? $data['extra_discount_amount'] : '',
                'sub_total' => isset($data['sub_total']) ? $data['sub_total'] : '',
                'grand_total' => isset($data['grand_total']) ? $data['grand_total'] : '',
                'address_id' => isset($data['address_id']) ? $data['address_id'] : '',
                'address_name' => isset($data['address']['address1']) ? $data['address']['address1'] . ' ' . $data['address']['address2'] . ' ' . $data['address']['landmark'] . ' ' . $data['address']['locality'] . ' ' . $data['address']['cityname']['city_name'] . ' ' . $data['address']['districtname']['district_name'] . ' ' . $data['address']['statename']['state_name'] . ' ' . $data['address']['countryname']['country_name'] . $data['address']['pincodename']['pincode'] : '',
            ]);
            return response()->json($order);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function uniqueValidation(Request $request)
    {
        $data = DB::table($request['table'])
            ->where(function ($query) use ($request) {
                if (!empty($request['id'])) {
                    $query->where('id', '!=', $request['id']);
                }
                if (!empty($request['customer_id'])) {
                    $query->where('customer_id', '!=', $request['customer_id']);
                }
                $query->where($request['column'], $request['value']);
            })
            ->first();
        if ($data) {
            return response()->json(false);
        } else {
            return response()->json(true);
        }
    }

    public function getCustomerLatLong(Request $request)
    {
        try {
            $data = Customers::where(function ($query) {
                $query->where('active', '=', 'Y');
            })
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->select('id', 'name', 'latitude', 'longitude')
                ->get();
            return response()->json($data);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getUppaidInvouces(Request $request)
    {
        try {
            $customer_id = $request->input('customer_id');
            $data = Invoice::where('user_id', '=', $customer_id)
                ->where('status_id', 0)
                ->select('id', 'invoice_date', 'invoice_no', 'grand_total', 'order_no', 'status_id', 'paid_amount')
                ->get();
            $sales = collect([]);
            if (!empty($data)) {
                foreach ($data as $key => $rows) {
                    $sales->push([
                        'id' => isset($rows['id']) ? $rows['id'] : '',
                        'invoice_date' => isset($rows['invoice_date']) ? $rows['invoice_date'] : '',
                        'invoice_no' => isset($rows['invoice_no']) ? $rows['invoice_no'] : '',
                        'grand_total' => isset($rows['grand_total']) ? $rows['grand_total'] : '',
                        'amount_unpaid' => isset($rows['paid_amount']) ? $rows['grand_total'] - $rows['paid_amount'] : $rows['grand_total'],
                        'order_id' => isset($rows['order_id']) ? $rows['order_id'] : '',
                        'status_id' => isset($rows['status_id']) ? $rows['status_id'] : '',
                    ]);
                }
            }
            return response()->json($sales);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function dashboardActivity(Request $request)
    {
        try {
            $reporting = UserReporting::where('userid', '=', Auth::user()->id)->select('users')->first();
            if (!empty($request->users)) {
                $users = $request->users;
            } else {
                $users = (!empty($reporting)) ? json_decode($reporting['users']) : [];
                array_push($users, Auth::user()->id);
            }
            $activities = collect([]);
            $customeractivity = Customers::where(function ($query) use ($users) {
                $query->whereDate('created_at', date('Y-m-d'));
                $query->whereIn('created_by', $users);
            })
                ->select('id', 'created_by', 'name', 'created_at')
                ->get();
            if ($customeractivity->isNotEmpty()) {
                $customeractivity->map(function ($item, $key) use ($activities) {
                    $activities->push([
                        'time' => date('H:i', strtotime($item->created_at)),
                        'profile' => isset($item['createdbyname']['profile_image']) ? $item['createdbyname']['profile_image'] : '',
                        'user_name' => isset($item['createdbyname']['name']) ? $item['createdbyname']['name'] : '',
                        'description' => $item['name'] . 'is Added in CRM',
                    ]);
                });
            }
            $checkinactivity = CheckIn::where(function ($query) use ($users) {
                $query->whereDate('checkin_date', date('Y-m-d'));
                $query->whereIn('user_id', $users);
            })
                ->select('customer_id', 'checkin_time', 'user_id')
                ->get();
            if ($checkinactivity->isNotEmpty()) {
                $checkinactivity->map(function ($item, $key) use ($activities) {
                    $activities->push([
                        'time' => date('G:i', strtotime($item->checkin_time)),
                        'profile' => isset($item['users']['profile_image']) ? $item['users']['profile_image'] : '',
                        'user_name' => isset($item['users']['name']) ? $item['users']['name'] : '',
                        'description' => $item['customers']['name'] . ' Counter Visited',
                    ]);
                });
            }
            $ordersactivity = Order::where(function ($query) use ($users) {
                $query->whereDate('order_date', date('Y-m-d'));
                $query->whereIn('created_by', $users);
            })
                ->select('id', 'grand_total', 'created_at', 'buyer_id', 'seller_id', 'created_by')
                ->get();
            if ($ordersactivity->isNotEmpty()) {
                $ordersactivity->map(function ($item, $key) use ($activities) {
                    $activities->push([
                        'time' => date('H:i', strtotime($item->created_at)),
                        'profile' => isset($item['createdbyname']['profile_image']) ? $item['createdbyname']['profile_image'] : '',
                        'user_name' => isset($item['createdbyname']['name']) ? $item['createdbyname']['name'] : '',
                        'description' => 'Received Order from ' . $item['buyers']['name']
                    ]);
                });
            }
            $sorted = $activities->sortByDesc('time');
            $collection = $sorted->values()->all();
            return response()->json($collection);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getUserLocationData(Request $request)
    {
        try {
            $users = $request->input('user_id');
            $date = !empty($request->input('date')) ? $request->input('date') : date('Y-m-d');
            $collection = UserLiveLocation::where(function ($query) use ($users, $date) {
                $query->whereDate('time', $date);
                $query->where('userid', $users);
            })
                ->select('address', 'time', 'latitude', 'longitude')
                ->get();
            return response()->json($collection);
        } catch (\Exception $e) {
            return $e;
        }
    }
    public function getUserActivityData(Request $request)
    {
        try {
            // $users = !empty($request->input('user_id')) ? $request->input('user_id') :Auth::user()->id ;
            // $date = !empty($request->input('date')) ? $request->input('date') : date('Y-m-d');
            // $collections = UserActivity::with('customers','users')->where(function($query) use($users, $date) {
            //                         $query->whereDate('created_at','=', date('Y-m-d',strtotime($date)));
            //                         $query->where('userid', $users);
            //                     })
            //                     ->select('customerid','latitude','longitude','time','address','description','type','userid')
            //                     ->get();
            $date = date('Y-m-d', strtotime($request->input('date')));
            $user_id = $request->input('user_id');

            $punchInOut = Attendance::where('user_id', $user_id)->where('punchin_date', $date)->get();
            $checkInOut = CheckIn::with('visitreports')->with('customers')->where('user_id', $user_id)->where('checkin_date', $date)->get();
            $orders = Order::with('buyers')->where('created_by', $user_id)->whereRaw('DATE(created_at)="' . $date . '"')->get();
            $customer_add = Customers::with('customeraddress')->where('created_by', $user_id)->whereRaw('DATE(created_at)="' . $date . '"')->get();
            $customer_update = Customers::with('customeraddress')->where('created_by', $user_id)->whereColumn('updated_at', '>', 'created_at')->whereRaw('DATE(updated_at)="' . $date . '"')->get();

            $punchInData = array();
            $punchOutData = array();
            $checkInData = array();
            $checkOutData = array();
            $orderData = array();
            $customerAddData = array();
            $customerUpdateData = array();

            foreach ($punchInOut as $k => $val) {
                if ($val->punchin_time != null) {
                    $punch_in_city = getLatLongToCity($val->punchin_latitude, $val->punchin_longitude);
                    $punchInData[$k]['title'] = 'Punchin';
                    $punchInData[$k]['time'] = $val->punchin_time;
                    $punchInData[$k]['latitude'] = $val->punchin_latitude != null ? $val->punchin_latitude : '';
                    $punchInData[$k]['longitude'] = $val->punchin_longitude != null ? $val->punchin_longitude : '';
                    $punchInData[$k]['msg'] = $val->punchin_summary . ' - ' . $punch_in_city;
                }
                if ($val->punchout_time != null) {
                    $punchOutData[$k]['title'] = 'Punchout';
                    $punchOutData[$k]['time'] = $val->punchout_time;
                    $punchOutData[$k]['latitude'] = $val->punchout_latitude != null ? $val->punchout_latitude : '';
                    $punchOutData[$k]['longitude'] = $val->punchout_longitude != null ? $val->punchout_longitude : '';
                    $punchOutData[$k]['msg'] = $val->punchout_address;
                }
            }

            foreach ($checkInOut as $k => $val) {
                if ($val->checkin_time != null) {
                    $check_in_city = getLatLongToCity($val->checkin_latitude, $val->checkin_longitude);
                    $checkInData[$k]['title'] = 'Checkin';
                    $checkInData[$k]['time'] = $val->checkin_time;
                    $checkInData[$k]['latitude'] = $val->checkin_latitude != null ? $val->checkin_latitude : '';
                    $checkInData[$k]['longitude'] = $val->checkin_longitude != null ? $val->checkin_longitude : '';
                    $checkInData[$k]['msg'] = $val->customers->name . ' - ' . $check_in_city;
                }
                if ($val->checkout_time != null) {
                    $check_out_city = getLatLongToCity($val->checkout_latitude, $val->checkout_longitude);
                    $checkOutData[$k]['title'] = 'Checkout';
                    $checkOutData[$k]['time'] = $val->checkout_time;
                    $checkOutData[$k]['latitude'] = $val->checkout_latitude != null ? $val->checkout_latitude : '';
                    $checkOutData[$k]['longitude'] = $val->checkout_longitude != null ? $val->checkout_longitude : '';
                    $checkOutData[$k]['msg'] = $val->customers->name . ' - ' . $check_out_city . '<br>Remark - ' . $val->visitreports->description;
                }
            }

            foreach ($orders as $k => $val) {
                $orderData[$k]['title'] = 'Order';
                $orderData[$k]['time'] = date('H:i:s', strtotime($val->created_at));
                $orderData[$k]['latitude'] = '';
                $orderData[$k]['longitude'] = '';
                $orderData[$k]['msg'] = $val->buyers->name . ' - ' . $val->buyers->customeraddress->cityname->city_name . ',<br>Qty : ' . $val->orderdetails->sum('quantity') . ',<br>Total : ' . $val->grand_total;
            }

            foreach ($customer_add as $k => $val) {
                $customerAddData[$k]['title'] = 'New Customer Registration';
                $customerAddData[$k]['time'] = date('H:i:s', strtotime($val->created_at));
                $customerAddData[$k]['latitude'] = $val->latitude;
                $customerAddData[$k]['longitude'] = $val->longitude;
                if ($val->customeraddress->cityname != null) {
                    $customerAddData[$k]['msg'] = $val->name . ' - ' . $val->customeraddress->cityname->city_name;
                } else {
                    $customerAddData[$k]['msg'] = $val->name . ' - City not enter';
                }
            }

            // foreach ($customer_update as $k => $val) {
            //     $customerUpdateData[$k]['title'] = 'Customer Edit';
            //     $customerUpdateData[$k]['time'] = date('H:i:s', strtotime($val->created_at));
            //     $customerUpdateData[$k]['latitude'] = $val->latitude;
            //     $customerUpdateData[$k]['longitude'] = $val->longitude;
            //     $customerUpdateData[$k]['msg'] = $val->name . ' - ' . $val->customeraddress?->cityname?->city_name;
            // }

            $collections = array_merge($punchInData, $punchOutData, $checkInData, $checkOutData, $orderData, $customerAddData, $customerUpdateData);

            usort($collections, function ($a, $b) {
                return strtotime($a['time']) - strtotime($b['time']);
            });
            foreach ($collections as $k => $val) {
                $collections[$k]['time'] = date('h:i A', strtotime($val['time']));
            }
            return response()->json($collections);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getCustomerActivityData(Request $request)
    {
        try {
            $notes = Notes::with('customerinfo', 'users')
                ->where(function ($query) use ($request) {
                    if (!empty($request['customer_id'])) {
                        $query->where('customer_id', $request['customer_id']);
                    }
                })
                ->select('id', 'user_id', 'customer_id', 'note', 'purpose', 'status_id', 'created_at', 'callstatus')
                ->latest()
                ->get();
            return response()->json($notes);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function removeSchemesdetails(Request $request)
    {
        try {
            $scheme_details = SchemeDetails::find($request->id);
            $scheme_details->delete();

            return response()->json(["status" => true]);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getCustomerDataSelect(Request $request)
    {
        if ($request->ajax()) {

            $term = trim($request->term);

            $coins = Customers::select("id as id", "name as text")->whereIN('customertype', ['1', '2', '3'])->where('name', 'LIKE',  '%' . $term . '%')->orderBy('id', 'asc')->simplePaginate(10);


            $morePages = true;
            $pagination_obj = json_encode($coins);
            if (empty($coins->nextPageUrl())) {
                $morePages = false;
            }
            $results = array(
                "results" => $coins->items(),
                "pagination" => array(
                    "more" => $morePages
                )
            );
            return response()->json($results);
        }
    }

    public function getProductDataSelect(Request $request)
    {
        if ($request->ajax()) {

            $term = trim($request->term);

            $coins = Product::select("id as id", "product_name as text")->where('product_name', 'LIKE',  '%' . $term . '%')->orderBy('id', 'asc')->simplePaginate(10);


            $morePages = true;
            $pagination_obj = json_encode($coins);
            if (empty($coins->nextPageUrl())) {
                $morePages = false;
            }
            $results = array(
                "results" => $coins->items(),
                "pagination" => array(
                    "more" => $morePages
                )
            );
            return response()->json($results);
        }
    }

    public function getStateDataSelect(Request $request)
    {
        if ($request->ajax()) {

            $term = trim($request->term);

            $coins = State::select("id as id", "state_name as text")->where('state_name', 'LIKE',  '%' . $term . '%')->orderBy('id', 'asc')->simplePaginate(10);


            $morePages = true;
            $pagination_obj = json_encode($coins);
            if (empty($coins->nextPageUrl())) {
                $morePages = false;
            }
            $results = array(
                "results" => $coins->items(),
                "pagination" => array(
                    "more" => $morePages
                )
            );
            return response()->json($results);
        }
    }

    public function getDealerDisDataSelect(Request $request)
    {
        if ($request->ajax()) {

            $term = trim($request->term);

            $coins = Customers::select("id as id", "name as text")->whereIN('customertype', ['1', '3'])->where('name', 'LIKE',  '%' . $term . '%')->orderBy('id', 'asc')->simplePaginate(10);


            $morePages = true;
            $pagination_obj = json_encode($coins);
            if (empty($coins->nextPageUrl())) {
                $morePages = false;
            }
            $results = array(
                "results" => $coins->items(),
                "pagination" => array(
                    "more" => $morePages
                )
            );
            return response()->json($results);
        }
    }

    public function getRetailerDataSelect(Request $request)
    {
        if ($request->ajax()) {

            $term = trim($request->term);

            $coins = Customers::select("id as id", "name as text")
                ->where('customertype', '2')
                ->where(function ($query) use ($term) {
                    $query->where('name', 'LIKE', '%' . $term . '%')
                        ->orWhere('mobile', 'LIKE', '%' . $term . '%');
                })
                ->orderBy('id', 'asc')
                ->simplePaginate(10);


            $morePages = true;
            $pagination_obj = json_encode($coins);
            if (empty($coins->nextPageUrl())) {
                $morePages = false;
            }
            $results = array(
                "results" => $coins->items(),
                "pagination" => array(
                    "more" => $morePages
                )
            );
            return response()->json($results);
        }
    }


    public function changeDocumnetStatus(Request $request)
    {
        if ($request->ajax()) {

            $column = $request->type;
            $customer_id = $request->customer_id;
            $status = $request->status;
            $update = CustomerDetails::where('customer_id', $customer_id)->update([$column => $status, 'status_update_by' => auth()->user()->id]);
            if ($request->status == '2') {
                switch ($request->type) {
                    case 'aadhar_no_status':
                        $update = CustomerDetails::where('customer_id', $customer_id)->update(['aadhar_no' => NULL, 'status_update_by' => auth()->user()->id]);
                        Attachment::where('customer_id', $customer_id)->where('document_name', 'aadhar')->delete();
                        Attachment::where('customer_id', $customer_id)->where('document_name', 'aadharback')->delete();
                        break;

                    case 'gstin_no_status':
                        $update = CustomerDetails::where('customer_id', $customer_id)->update(['gstin_no' => NULL, 'status_update_by' => auth()->user()->id]);
                        Attachment::where('customer_id', $customer_id)->where('document_name', 'gstin')->delete();
                        break;

                    case 'pan_no_status':
                        $update = CustomerDetails::where('customer_id', $customer_id)->update(['pan_no' => NULL, 'status_update_by' => auth()->user()->id]);
                        Attachment::where('customer_id', $customer_id)->where('document_name', 'pan')->delete();
                        break;

                    case 'bank_status':
                        $update = CustomerDetails::where('customer_id', $customer_id)->update(['account_holder' => NULL, 'account_number' => NULL, 'bank_name' => NULL, 'ifsc_code' => NULL]);
                        Attachment::where('customer_id', $customer_id)->where('document_name', 'bankpass')->delete();
                        break;

                    case 'otherid_no_status':
                        $update = CustomerDetails::where('customer_id', $customer_id)->update(['otherid_no' => NULL]);
                        Attachment::where('customer_id', $customer_id)->where('document_name', 'other')->delete();
                        break;

                    default:

                        break;
                }
            }
            $customer = Customers::with('customerdetails')->find($customer_id);
            if ($update) {
                if ($status == 1) {
                    $msg = "Verified Successfully !!";
                    $title = 'KYC Approval';
                    $pmsg = 'KYC is Approved ✅';
                } elseif ($status == 2) {
                    $msg = "Rejected Successfully !!";
                    $title = 'KYC Rejection 🚫';
                    $pmsg = 'KYC is Rejected';
                } else {
                    $msg = "";
                }
                $noti_data = [
                    'fcm_token' =>  $customer->customerdetails->fcm_token,
                    'title' => $title,
                    'msg' => $pmsg,
                ];
                $send_notification = SendNotifications::send($noti_data);
                $results = array(
                    "status" => true,
                    "msg" => $msg
                );
            } else {
                $results = array(
                    "status" => false,
                    "msg" => "Somthing went wrong"
                );
            }
            return response()->json($results);
        }
    }

    public function getGiftSubCategoryData(Request $request)
    {
        try {
            $data = GiftSubcategory::where(function ($query) {
                $query->where('active', '=', 'Y');
            });
            if ($request->cat_id && $request->cat_id != null && $request->cat_id != '') {
                $data->where('category_id', $request->cat_id);
            }
            $data = $data->select('id', 'subcategory_name')
                ->orderBy('subcategory_name', 'asc')
                ->get();
            return response()->json($data);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getExpensesData(Request $request)
    {
        if ($request->ajax()) {

            $term = trim($request->term);

            $coins = Expenses::select("id as id", "id as text")->where('id', 'LIKE',  '%' . $term . '%')->orderBy('id', 'desc')->simplePaginate(10);


            $morePages = true;
            $pagination_obj = json_encode($coins);
            if (empty($coins->nextPageUrl())) {
                $morePages = false;
            }
            $results = array(
                "results" => $coins->items(),
                "pagination" => array(
                    "more" => $morePages
                )
            );
            return response()->json($results);
        }
    }

    public function getGiftModelData(Request $request)
    {
        try {
            $data = GiftModel::where(function ($query) {
                $query->where('active', '=', 'Y');
            });
            if ($request->cat_id && $request->cat_id != null && $request->cat_id != '') {
                $data->where('sub_category_id', $request->cat_id);
            }
            $data = $data->select('id', 'model_name')
                ->orderBy('model_name', 'asc')
                ->get();
            return response()->json($data);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getBankdetailandPoints(Request $request)
    {
        try {
            $shop_img = Customers::where('id', $request->cust_id)->value('profile_image');
            $customer_bank_details = CustomerDetails::select('account_number', 'account_holder', 'ifsc_code', 'bank_name', 'bank_status')->where('customer_id', $request->cust_id)->first();
            $customer_aadhar_details = CustomerDetails::select('aadhar_no', 'aadhar_no_status')->where('customer_id', $request->cust_id)->first();
            $thistorys = TransactionHistory::where('customer_id', $request->cust_id)->get();
            $active_points = 0;
            $provision_points = 0;
            foreach ($thistorys as $thistory) {
                if ($thistory->status == '1') {
                    $active_points += $thistory->point;
                } else {
                    $active_points += $thistory->active_point;
                    $provision_points += $thistory->provision_point;
                }
            }
            $total_redemption = Redemption::where('customer_id', $request->cust_id)->whereNot('status', '2')->sum('redeem_amount') ?? 0;
            $total_balance = (int)$active_points - (int)$total_redemption;

            $data['bank_details'] = $customer_bank_details;
            $data['aadhar_details'] = $customer_aadhar_details;
            $data['Total_points'] = $total_balance;
            $data['shop_img'] = $shop_img;

            return response()->json($data);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getProductByCoupon(Request $request)
    {
        try {
            $serial_no = $request->serial_no;
            $serial_no_product_code = Services::where('serial_no', $serial_no)->value('product_code');
            $service = Services::where('serial_no', $serial_no)->first();
            $all_products = Product::all();
            $html = '<option value="">Select Product</option>';
            $slected = false;
            foreach ($all_products as $product) {
                if ($serial_no_product_code && $product->product_code == $serial_no_product_code && $serial_no_product_code != null && $serial_no_product_code != '') {
                    $html .= '<option value="' . $product->id . '" selected>' . $product->product_name . '</option> ';
                    $slected = true;
                } else {
                    $html .= '<option value="' . $product->id . '">' . $product->product_name . '</option> ';
                }
            }
            $data['status'] = true;
            $data['html'] = $html;
            $data['service'] = $service;
            $data['slected'] = $slected;
            return response()->json($data);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getTourPlanByUserAndDate(Request $request)
    {
        try {
            $data = TourProgramme::where('date', $request->date)->where('userid', $request->user_id)->first();
            if ($data && $data != NULL && !empty($data)) {
                $response = ['status' => true, 'data' => $data];
                return response()->json($response);
            } else {
                $response = ['status' => false, 'data' => $data];
                return response()->json($response);
            }
        } catch (\Exception $e) {
            return $e;
        }
    }
    public function userCityList(Request $request)
    {
        try {
            $cityname = $request->input('cityname');
            $user_id = $request->user()->id;
            $cityids = UserCityAssign::where('userid', '=', $user_id)->pluck('city_id')->toArray();
            //$data = City::whereIn('id',$cityids)->select('id','city_name', 'grade')->orderBy('city_name','asc')->get();

            $data = City::whereIn('id', $cityids)->select('id', 'city_name', 'grade');
            if ($cityname) {
                $data->where('city_name', 'LIKE', trim($cityname) . '%');
            }
            $data = $data->orderBy('city_name', 'asc')->get();

            if ($data->isNotEmpty()) {
                return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $data], 200);
            }
            return response(['status' => 'error', 'message' => 'No Record Found.', 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function getProductInfoBySerialNo(Request $request)
    {
        try {
            $serial_no = $request->input('serial_no');
            if ($serial_no != NULL && $serial_no != '') {
                $data = Services::with('product', 'branch')
                    ->where('serial_no', $serial_no)
                    ->latest()->first();
                // dd($data);
                if ($data) {
                    $data->product->categories = $data->product->categories;
                    $data->product->subcategories = $data->product->subcategories;
                    $check_Warranty = WarrantyActivation::with('media', 'customer', 'seller_details')->where('status', '!=', '3')->where('product_serail_number', $serial_no)->first();
                    $encrypt_id = "";

                    if (isset($check_Warranty)) {
                        $encrypt_id = Crypt::encrypt($check_Warranty->id) ?? '';
                    }
                    return response()->json(['status' => true, 'data_all' => $data, 'data' => $data->product, 'check_Warranty' => $check_Warranty, 'encrypt_id' => $encrypt_id]);
                } else {
                    return response()->json(['status' => false, 'data' => null]);
                }
            } else {
                return response()->json(['status' => false, 'data' => null]);
            }
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getEndUserData(Request $request)
    {
        try {
            $customer_number = $request->input('customer_number');
            if (isset($customer_number)) {
                $data = EndUser::where(function ($query) use ($customer_number) {
                    $query->where('customer_number', '=', $customer_number);
                })
                    ->first();
                if ($data) {
                    $state = State::where('state_name', $data->customer_state)->first();
                    $district = District::where('district_name', $data->customer_district)->first();
                    $city = City::where('city_name', $data->customer_city)->first();
                    if ($state && $state != NULL && !empty($state) && $data->state_id == NULL) {
                        $data->state_id = $state->id;
                    }
                    if ($district && $district != NULL && !empty($district) && $data->district_id == NULL) {
                        $data->district_id = $district->id;
                    }
                    if ($city && $city != NULL && !empty($city) && $data->city_id == NULL) {
                        $data->city_id = $city->id;
                    }
                    return response()->json(['status' => true, 'data' => $data]);
                } else {
                    return response()->json(['status' => false, 'data' => null]);
                }
            } else {
                return response()->json(['status' => false, 'data' => null]);
            }
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getComplaintsData(Request $request)
    {
        try {
            $search = $request->input('search');
            $end_user = EndUser::where('customer_number', $search)->first();
            $data = Complaint::where(function ($query) use ($search, $end_user) {
                if (isset($search)) {
                    $query->where('product_serail_number', '=', $search);
                }
                if (isset($end_user) && $end_user && $end_user != NULL) {
                    $query->orwhere('end_user_id', '=', $end_user->id);
                }
            })
                ->get();
            if (count($data) > 0) {
                $html = '';
                foreach ($data as $val) {
                    $html .= '<tr><td>';
                    $html .= $val->complaint_number;
                    $html .= '</td><td>';
                    $html .= date('d M Y', strtotime($val->complaint_date));
                    $html .= '</td><td>';
                    $html .= strtoupper($val->product_serail_number);
                    $html .= '</td><td>';
                    $html .= $val->claim_amount;
                    $html .= '</td><td>';
                    if ($val->complaint_status == '0') {
                        $html .= 'Open';
                    } elseif ($val->complaint_status == '1') {
                        $html .= 'Pending';
                    }

                    $html .= '</td><td>';
                    $html .= $val->service_center_details ? $val->service_center_details->name : '';
                    $html .= '</td><td>';
                    $html .= $val->seller_details ? $val->seller_details->name : '';
                    $html .= '</td><td>';
                    $html .= $val->party ? $val->party->name : '';
                    $html .= '</td><td>';
                    $html .= '</td><tr>';
                }
                return response()->json(['status' => true, 'data' => $html]);
            } else {
                $data = '<tr><td class="text-center" colspan="8">No record Found</td></tr>';
                return response()->json(['status' => false, 'data' => $data]);
            }
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function fetchPieChartData()
    {
        $labels = ['Active', 'Provision', 'Redeem'];
        $total_points = TransactionHistory::sum('point') ?? 0;
        $active_points = TransactionHistory::where('status', '1')->sum('point') ?? 0;
        $provision_points = TransactionHistory::where('status', '0')->sum('point') ?? 0;
        $total_redemption = Redemption::whereNot('status', '2')->sum('redeem_amount') ?? 0;
        $total_rejected = Redemption::where('status', '2')->sum('redeem_amount') ?? 0;
        $total_balance = (int)$active_points - (int)$total_redemption;
        $values = [$active_points, $provision_points, $total_redemption];

        return response()->json([
            'labels' => $labels,
            'values' => $values
        ]);
    }

    public function remove_session(Request $request)
    {
        $request->session()->forget('executive_id');
        return response()->json(['status' => 'success']);
    }

    public function getComplaintsDataProduct(Request $request)
    {
        $complaint = Complaint::with('createdbyname')->where('complaint_number', $request->complaint_number)->first();
        $service_bill = ServiceBill::where('complaint_no', $request->complaint_number)->first();

        $product = $complaint->product_details ?? '';

        $data['complaint'] = $complaint;
        $data['product'] = $product;
        $data['service_bill'] = $service_bill;

        return response()->json(['status' => 'success', 'data' => $data]);
    }

    public function getServiceCategory(Request $request)
    {
        if ($request->ajax()) {

            $data = ServiceChargeCategories::where('division_id', $request->division_id)->get();

            return response()->json($data);
        }
    }

    public function getPrimaryTotal(Request $request)
    {
        $query = PrimarySales::query();
        if ($request->user_id && $request->user_id != '' && $request->user_id != null) {
            $usersIds = User::where('id', $request->user_id)->where('sales_type', 'Secondary')->pluck('id');
        } else {
            $usersIds = User::with('attendance_details')->where('sales_type', 'Secondary')->pluck('id');
        }

        $role = Role::find(29);
        if ($role && auth()->user()->hasRole($role->name)) {
            $child_customer = ParentDetail::where('parent_id', auth()->user()->customerid)
                ->pluck('id')
                ->push(auth()->user()->customerid);
            $query->whereIn('customer_id', $child_customer);
        }

        if ($request->branch_id && $request->branch_id != '' && $request->branch_id != null) {
            $query->where('final_branch', $request->branch_id);
        }

        if ($request->division_id && $request->division_id != '' && $request->division_id != null) {
            $query->whereIn('division', $request->division_id);
        }

        if ($request->dealer_id && $request->dealer_id != '' && $request->dealer_id != null) {
            $query->where('dealer', 'like', '%' . $request->dealer_id . '%');
        }

        if ($request->product_model && $request->product_model != '' && $request->product_model != null) {
            $query->where('product_name', $request->product_model);
        }

        if ($request->new_group && $request->new_group != '' && $request->new_group != null) {
            $query->where('new_group', $request->new_group);
        }

        if ($request->executive_id && $request->executive_id != '' && $request->executive_id != null) {
            $query->where('sales_person', $request->executive_id);
        }

        if ($request->financial_year && $request->financial_year != '' && $request->financial_year != null) {
            $f_year_array = explode('-', $request->financial_year);

            $startDateFormatted = $f_year_array[0] . '-04-01';
            $endDateFormatted = $f_year_array[1] . '-03-31';
        }

        if ($request->month && $request->month != '' && $request->month != null && $request->financial_year && $request->financial_year != '' && $request->financial_year != null) {

            $f_year_array = explode('-', $request->financial_year);
            if (array_intersect($request->month, ['Jan', 'Feb', 'Mar'])) {
                $currentYear = $f_year_array[1];
                $monthNumbers = array_map(function ($month) {
                    return Carbon::parse($month)->month;
                }, $request->month);

                // Get the first month number and the last month number
                $firstMonthNumber = min($monthNumbers);
                $lastMonthNumber = max($monthNumbers);

                // Create Carbon instances for the first and last dates
                $firstDate = Carbon::createFromDate($currentYear, $firstMonthNumber, 1)->startOfMonth();
                $lastDate = Carbon::createFromDate($currentYear, $lastMonthNumber, 1)->endOfMonth();
                $startDateFormatted = $firstDate->toDateString();
                $endDateFormatted = $lastDate->toDateString();
            } else {
                $currentYear = $f_year_array[0];
                $monthNumbers = array_map(function ($month) {
                    return Carbon::parse($month)->month;
                }, $request->month);

                // Get the first month number and the last month number
                $firstMonthNumber = min($monthNumbers);
                $lastMonthNumber = max($monthNumbers);

                // Create Carbon instances for the first and last dates
                $firstDate = Carbon::createFromDate($currentYear, $firstMonthNumber, 1)->startOfMonth();
                $lastDate = Carbon::createFromDate($currentYear, $lastMonthNumber, 1)->endOfMonth();
                $startDateFormatted = $firstDate->toDateString();
                $endDateFormatted = $lastDate->toDateString();
            }
        }

        $query->where(function ($q) use ($startDateFormatted, $endDateFormatted) {
            $q->where('invoice_date', '>=', $startDateFormatted)
                ->where('invoice_date', '<=', $endDateFormatted);;
        });

        $data['total_qty'] = $query->sum('quantity');
        $data['total_sale'] = number_format(($query->sum('net_amount') / 100000), 2, '.', '') . " (Lac)";

        return response()->json($data);
    }

    public function getServiceProduct(Request $request)
    {
        if ($request->ajax()) {
            $categoryIds = explode(',', $request->pro_sub_cat);
            $data = ServiceChargeProducts::where('charge_type_id', $request->charge_type_id)->where('division_id', $request->charge_cat_id)->whereIn('category_id', $categoryIds)->get();

            return response()->json($data);
        }
    }

    public function getServiceProductDetails(Request $request)
    {
        if ($request->ajax()) {

            $data = ServiceChargeProducts::where('id', $request->id)->first();

            return response()->json($data);
        }
    }

    public function changeAppointmentStatus(Request $request)
    {
        if ($request->status == '3') {
            $update = DealerAppointment::where('id', $request->appo_id)->update(['approval_status' => $request->status, 'ho_approve' => auth()->user()->id, 'ho_approve_date' => date('Y-m-d')]);
            DealerAppointmentKyc::updateOrCreate(
                ['appointment_id' => $request->appo_id],
                ['dealer_code' => $request->dealer_code]
            );
        } else {
            if ($request->status == '1') {
                $update = DealerAppointment::where('id', $request->appo_id)->update(['approval_status' => $request->status, 'sales_approve' => auth()->user()->id]);
            } else {
                $update = DealerAppointment::where('id', $request->appo_id)->update(['approval_status' => $request->status]);
            }
        }
        if ($update) {
            return response()->json(['status' => 'success', 'message' => 'Approved Successfully !!']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Somthing went wrong.']);
        }
    }

    public function getWorkDoneTime(Request $request)
    {
        $work_done = ComplaintWorkDone::where('complaint_id', $request->complaint_id)->latest()->first();
        $service_center = ComplaintTimeline::where('complaint_id', $request->complaint_id)->where('status', '101')->latest()->first();

        $start_date_time = $service_center->created_at;
        $end_date_time = $work_done->created_at;

        $diff = $start_date_time->diff($end_date_time);

        $hoursDifference = $diff->h;
        $minutesDifference = $diff->i;

        $totalHours = $diff->days * 24 + $hoursDifference + ($minutesDifference / 60);

        return response()->json(['status' => 'success', 'hours' => $totalHours]);
    }

    public function getPrimarySachme(Request $request)
    {
        $pSchemes = PrimaryScheme::where('quarter', $request->quater)->whereIn('division', $request->division)->select('id', 'scheme_name')->get();
        return response()->json(['status' => 'success', 'data' => $pSchemes]);
    }

    public function getExpenseCount(Request $request)
    {
        if ($request->end_date && !empty($request->end_date)) {
            $data['pending_count'] = Expenses::where('checker_status', '0')->where('date', '>=', $request->start_date)->where('date', '<=', $request->end_date)->count();
            $data['approve_count'] = Expenses::where('checker_status', '1')->where('date', '>=', $request->start_date)->where('date', '<=', $request->end_date)->count();
            $data['reject_count'] = Expenses::where('checker_status', '2')->where('date', '>=', $request->start_date)->where('date', '<=', $request->end_date)->count();
            $data['checked_count'] = Expenses::where('checker_status', '3')->where('date', '>=', $request->start_date)->where('date', '<=', $request->end_date)->count();
        } else {
            $data['pending_count'] = Expenses::where('checker_status', '0')->where('date', '>=', $request->start_date)->count();
            $data['approve_count'] = Expenses::where('checker_status', '1')->where('date', '>=', $request->start_date)->count();
            $data['reject_count'] = Expenses::where('checker_status', '2')->where('date', '>=', $request->start_date)->count();
            $data['checked_count'] = Expenses::where('checker_status', '3')->where('date', '>=', $request->start_date)->count();
        }

        return response()->json(['status' => 'success', 'data' => $data]);
    }


    public function marketingGetCounts(Request $request)
    {
        $data = Marketing::query();

        if ($request->state) {
            $data->where('state', $request->state);
        }

        if ($request->district) {
            $data->where('event_district', $request->district);
        }

        if ($request->event_under) {
            $data->where('event_under_name', $request->event_under);
        }

        if ($request->branch) {
            $data->where('branch', $request->branch);
        }

        if ($request->event_center) {
            $data->where('event_center', $request->event_center);
        }

        if ($request->category_of_participant) {
            $data->where('category_of_participant', $request->category_of_participant);
        }

        if ($request->branding_team_member != null && $request->branding_team_member != '') {
            $data->where('branding_team_member', $request->branding_team_member);
        }

        if ($request->start_date) {
            $data->where('event_date', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $data->where('event_date', '<=', $request->end_date);
        }

        // Clone the query for independent counts
        $total = $data->count();
        $plumber_count = (clone $data)->where('category_of_participant', 'Plumber')->count();
        $mechanic_count = (clone $data)->where('category_of_participant', 'Mechanic')->count();
        $village_influencer_count = (clone $data)->where('category_of_participant', 'Village influencer')->count();
        $retailer_count = (clone $data)->where('category_of_participant', 'Retailer')->count();
        $electrician_count = (clone $data)->where('category_of_participant', 'Electrician')->count();
        $exhibition_count = (clone $data)->where('category_of_participant', 'Exhibition Visitors')->count();

        return response()->json([
            'total' => $total,
            'plumber_count' => $plumber_count,
            'mechanic_count' => $mechanic_count,
            'village_influencer_count' => $village_influencer_count,
            'retailer_count' => $retailer_count,
            'electrician_count' => $electrician_count,
            'exhibition_count' => $exhibition_count
        ]);
    }

    public function getLeaveBalance(Request $request)
    {
        $data = User::where('id', $request->user_id)->first();
        $last60Days = Carbon::now()->subDays(60);
        $comp_off_balance = CompOffLeave::where('comp_off_date', '>=', $last60Days)->where('is_used', false)
            ->where('user_id', $request->user_id)
            ->sum('balance');
        return response()->json(['status' => 'success', 'leave_balance' => $data->leave_balance, 'comp_off_balance' => $comp_off_balance]);
    }


    // getproduct inteval time
    public function getProductTimeInterval(Request $request)
    {
        $product = Product::find($request->product_id);
        if (!isset($product)) {
            return response()->json(['status' => 'error', 'product' => "Not Found"]);
        }
        $date = Carbon::parse($request->sale_bill_date);
        $warranty_expire_date = '';
        if ($product->expiry_interval == "Month") {
            $warranty_expire_date = $date->addMonths($product->expiry_interval_preiod)->format('d-m-Y');
        } else if ($product->expiry_interval == "Day") {
            $warranty_expire_date = $date->addDays($product->expiry_interval_preiod)->format('d-m-Y');
        } else if ($product->expiry_interval == "Year") {
            $warranty_expire_date = $date->addYears($product->expiry_interval_preiod)->format('d-m-Y');
        }
        return response()->json(['status' => 'success', 'warrenty_expire_date' => $warranty_expire_date]);
    }

    public function getProductWarrentyTime(Request $request)
    {
        $product = Product::find($request->product_id);
        if (!isset($product)) {
            return response()->json(['status' => 'error', 'product' => "Not Found"]);
        }

        $warrenty_time = $product->expiry_interval_preiod ?? 0;;
        if ($product->expiry_interval == "Month") {
            $warrenty_time = $product->expiry_interval_preiod;
        } else if ($product->expiry_interval == "Day") {
            $warrenty_time = round($product->expiry_interval_preiod / 30);
        } else if ($product->expiry_interval == "Year") {
            $warrenty_time = round($product->expiry_interval_preiod * 12);
        }
        return response()->json(['status' => 'success', 'warrenty_time' => $warrenty_time]);
    }

    // get seller or buyre
    public function getSellerBuyer(Request $request)
    {
        $userids = getUsersReportingToAuth();
        $query = Customers::query();

        // Filter by seller or buyer type
        if ($request->is_seller == 1) {
            $query->where(function ($query) use ($userids) {
                if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
                    if (Auth::user()->hasRole('Accounts Order')) {
                        $userids = User::whereIn('branch_id', explode(',', Auth::user()->branch_show))->pluck('id');
                    }
                    $query->whereIn('executive_id', $userids)
                        ->orWhereIn('created_by', $userids);
                }
            });
        } elseif ($request->is_seller == 0) {
            $query->whereIn('customertype', ['1', '3', '4', '5', '6'])
                ->where(function ($query) use ($userids) {
                    if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
                        $query->whereIn('executive_id', $userids)
                            ->orWhereIn('created_by', $userids);
                    }
                });
        }

        // Search functionality
        if (!empty($request->q)) {
            $query->where('name', 'LIKE', '%' . $request->q . '%')
                ->orWhere('mobile', 'LIKE', '%' . $request->q . '%');
        }

        // Pagination
        $users = $query->where('active', 'Y')
            ->select('id', 'name')
            ->paginate(10); // Adjust limit per page

        if ($request->selected) {
            $selectedUser = Customers::find($request->selected);
            if ($selectedUser) {
                // Add the selected user at the top of the list
                $users->prepend($selectedUser);
            }
        }

        return response()->json([
            'results' => $users->items(),
            'pagination' => ['more' => $users->hasMorePages()],
        ]);
    }

    // get pincode
    public function getPincodeSearch(Request $request)
    {
        $query = Pincode::query();

        // Search functionality
        if (!empty($request->q)) {
            $query->where('pincode', 'LIKE', '%' . $request->q . '%');
        }

        // Pagination
        $pincodes = $query->where('active', 'Y')->paginate(10); // Adjust limit per page

        if ($request->selected) {
            $selectedPincode = Pincode::find($request->selected);
            if ($selectedPincode) {
                $pincodes->prepend($selectedPincode);
            }
        }

        return response()->json([
            'results' => $pincodes->items(), // Corrected from `$users` to `$pincodes`
            'pagination' => ['more' => $pincodes->hasMorePages()],
        ]);
    }

    // get All Seller by pagination 
    public function getAllCustomer(Request $request)
    {
        $query = Customers::query();

        // Search functionality
        if (!empty($request->q)) {
            $query->where('name', 'LIKE', '%' . $request->q . '%')
                ->Orwhere('mobile', 'LIKE', '%' . $request->q . '%');
        }

        // Pagination
        $customers = $query->where('active', 'Y')->paginate(10); // Adjust limit per page

        if ($request->selected) {
            $selected_customer = Pincode::find($request->selected);
            if ($selected_customer) {
                $customers->prepend($selected_customer);
            }
        }

        return response()->json([
            'results' => $customers->items(), // Corrected from `$users` to `$pincodes`
            'pagination' => ['more' => $customers->hasMorePages()],
        ]);
    }

    public function getAllPartyName(Request $request)
    {
        $query = Customers::query();

        // Search functionality
        if (!empty($request->q)) {
            $query->where('name', 'LIKE', '%' . $request->q . '%')
                ->Orwhere('mobile', 'LIKE', '%' . $request->q . '%');
        }

        // Pagination
        $customers = $query->where('active', 'Y')->paginate(10); // Adjust limit per page

        if ($request->selected) {
            $selected_customer = Customers::where(['name' => $request->selected])->first();
            if ($selected_customer) {
                $customers->prepend($selected_customer);
            }
        }

        return response()->json([
            'results' => $customers->items(), // Corrected from `$users` to `$pincodes`
            'pagination' => ['more' => $customers->hasMorePages()],
        ]);
    }

    // Add Marketing actinity 
    public function addMarketingType(Request $request)
    {
        $type = $request->type ?? '';
        $activity_division = $request->activity_division ?? '';
        if (isset($type)) {
            $slug = strtolower(str_replace(' ', '_', $type));
            $marketing_type = MarketingActivity::updateOrCreate(
                [
                    'slug' => $slug,
                    'activity_division' => $activity_division
                ],
                ['type' => $type]
            );
            return response()->json(['status' => true, 'marketing_type' => $marketing_type]);
        }
        return response()->json(['status' => false]);
    }

    public function getMarketingType(Request $request)
    {
        try {
            $types = MarketingActivity::with('division')->select('id', 'type', 'activity_division')->get(); // Fetching data

            $html = '';
            foreach ($types as $type) {
                $divisionName = $type->division ? $type->division->division_name : 'No Division';
                $html .= '
                    <span class="badge badge-info mr-2 mb-2" id="badge_' . $type->id . '">
                        ' . e($divisionName . ' - ' . $type->type) . '
                        <button type="button" class="close" aria-label="Close" onclick="removeBadge(' . $type->id . ')">
                            <span aria-hidden="true" style="color:red">&times;</span>
                        </button>
                    </span>
                ';
            }

            return response()->json([
                'status' => true,
                'message' => 'Marketing types retrieved successfully',
                'html' => $html
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteMarketingType(Request $request)
    {
        try {
            $id = $request->id;

            $marketingType = MarketingActivity::find($id);
            if (!$marketingType) {
                return response()->json([
                    'status' => false,
                    'message' => 'Marketing type not found'
                ], 404);
            }

            $marketingType->delete();

            return response()->json([
                'status' => true,
                'message' => 'Marketing type deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    // get users by branch id
    public function getUserByBranch(Request $request)
    {
        $customer_city = $request->customer_city ?? '';
        $roleNames = ["Service Eng", "Service Admin"];
        $assign_users = User::whereHas('roles', function ($query) use ($roleNames) {
            $query->whereIn('name', $roleNames); // Filter users by roles
        })
            ->whereHas('cities', function ($query) use ($customer_city) {
                $query->where('city_id', $customer_city); // Filter users assigned to the specific city
            })
            ->with(['roles' => function ($query) {
                $query->with('permissions'); // Include role permissions
            }])
            ->select('id', 'name', 'employee_codes')
            ->get();

        $html = '<option value="">Select User</option>'; // Default option

        $id = $assign_users[0]->id ?? '';
        if ($assign_users->isNotEmpty()) {
            foreach ($assign_users as $user) {
                $html .= '<option value="' . $user->id . '">' . $user->name . '</option>';
            }
        }
        return response()->json(['html' => $html, 'id' => $id]);
    }



    public function getUserCity(Request $request)
    {
        $branch_id = $request->customer_city ?? '';
        $users = User::where(['branch_id' => $branch_id])->get();
        $html = '<option value="">Select User</option>'; // Default option

        if ($users->isNotEmpty()) {
            foreach ($users as $user) {
                $html .= '<option value="' . $user->id . '">' . $user->name . '</option>';
            }
        }
        return response()->json(['html' => $html]);
    }

    public function getPMS(Request $request)
    {
        $f_year_array = explode('-', $request->financial_year);

        $start_date = $f_year_array[0] . '-04-01';
        $end_date = $f_year_array[1] . '-03-31';
        if ($end_date > now()->toDateString()) {
            $end_date = now()->toDateString();
        }

        $user = User::with('getbranch', 'getdivision', 'getdesignation', 'all_attendance_details', 'visits', 'customers', 'userinfo', 'target', 'primarySales')->where('id', $request->user_id);

        $user = $user->withCount([
            'all_attendance_details as working_days' => function ($user) use ($start_date, $end_date) {
                $user->whereNotIn('working_type', ['Office Work', 'Office Meeting', 'Full Day Leave', 'Leave', 'Holiday'])
                    ->whereBetween('punchin_date', [$start_date, $end_date]);
            },
            'visits as visit_count' => function ($user) use ($start_date, $end_date) {
                $user->whereBetween('checkin_date', [$start_date, $end_date]);
            }
        ]);

        $user = $user->first();

        if (empty(array_diff($request->role_id, [2, 32]))) {

            if (isset($user['userinfo']['date_of_joining']) && $user['userinfo']['date_of_joining'] != null && $user['userinfo']['date_of_joining'] > $start_date && $user['userinfo']['date_of_joining'] < $end_date) {
                $startDate = Carbon::parse($startDate = Carbon::parse($user['userinfo']['date_of_joining']));
            } else {
                $startDate = Carbon::parse($start_date);
            }
            $endDate = Carbon::parse($end_date);
            $monthCount = $startDate->diffInMonths($endDate) + 1;

            $selectedmonths = [];
            while ($startDate->lessThanOrEqualTo($endDate)) {
                $selectedmonths[] = $startDate->format('M');
                $startDate->addMonth();
            }

            $working_days_trg = 20 * $monthCount;
            $visit_count_trg = 120 * $monthCount;
            $unique_visit_count_trg = 8 * $monthCount;
            $active_customer_trg = 8 * $monthCount;
            $msp_activity_trg = 4 * $monthCount;

            $unique_visit_count = $user->visits
                ->whereBetween('checkin_date', [$start_date, $end_date])
                ->filter(function ($visit) use ($request) {
                    $city_id = optional(optional($visit->customers)->customeraddress)->city_id;

                    if (!$city_id) {
                        return true;
                    }

                    $existing_customer = Customers::whereHas('customeraddress', function ($q) use ($city_id) {
                        $q->where('city_id', $city_id);
                    })
                        ->whereHas('createdbyname', function ($q) use ($request) {
                            $q->where('division_id', $request->division_id);
                        })
                        ->where('created_at', '<', $visit->checkin_date)
                        ->exists();
                    return !$existing_customer;
                })
                ->unique(fn($visit) => optional(optional($visit->customers)->customeraddress)->city_id)
                ->count();

            $user_target = $user->target->whereIn('month', $selectedmonths)->sum('target');
            $user_achiv = $user->primarySales->where('invoice_date', '>=', $start_date)->where('invoice_date', '<=', $end_date)->sum('net_amount');
            $user_achiv_new_dealer = $user->primarySales()->where('invoice_date', '>=', $start_date)->where('invoice_date', '<=', $end_date)->where('new_dealer', 'Y')->sum('net_amount');
            $user_achiv_new_product = $user->primarySales()->where('invoice_date', '>=', $start_date)->where('invoice_date', '<=', $end_date)->where('new_product', 'Y')->sum('net_amount');
            DB::statement("SET SESSION group_concat_max_len = 10000000");

            $total_assign_customer_ids = EmployeeDetail::where('user_id', $user->id)
                ->pluck('customer_id')
                ->toArray();

            $child_customer_ids = ParentDetail::whereIn('parent_id', $total_assign_customer_ids)
                ->pluck('customer_id')
                ->toArray();

            $all_customer_ids = array_merge($total_assign_customer_ids, $child_customer_ids);
            $active_customer = 0;

            foreach (array_chunk($all_customer_ids, 500) as $chunk) {
                $active_customer += TransactionHistory::whereBetween('created_at', [$start_date, $end_date])
                    ->whereIn('customer_id', $chunk)
                    ->whereNotIn('customer_id', function ($query) use ($start_date) {
                        $query->select('customer_id')
                            ->from('transaction_histories')
                            ->where('created_at', '<', $start_date);
                    })
                    ->groupBy('customer_id')
                    ->selectRaw('customer_id')
                    ->get()
                    ->count();
            }

            $debtors_start_date = $f_year_array[0] . '-04-01';

            $debtors_end_date = now()->toDateString();

            $debtors_start_date_or = Carbon::createFromFormat('Y-m-d', $f_year_array[0] . '-04-01');
            $debtors_end_date_or = now();

            $days_difference = $debtors_start_date_or->diffInDays($debtors_end_date_or);

            $debtors_sales = PrimarySales::where('branch_id', $user->branch_id)->where('invoice_date', '>=', $debtors_start_date)->where('invoice_date', '<=', $debtors_end_date)->whereIn('division', ['PUMP', 'MOTOR'])->sum('net_amount');
            $total_debtors = CustomerOutstanting::where('branch_id', $user->branch_id)->whereIn('division_id', ['10', '18'])->where('year', $f_year_array[0])->sum('amount');

            $degree_name = array();
            if (!empty($user['geteducation'])) {
                foreach ($user['geteducation'] as $key_new => $datas) {
                    $degree_name[] = isset($datas->degree_name) ? $datas->degree_name : '';
                }
            }

            $msp_activitys = MspActivity::where('emp_code', $user->employee_codes);
            if (isset($request->month) && count($request->month) > 0) {
                $msp_activitys->whereIn('month', $request->month);
            }
            $msp_activitys = $msp_activitys->where('fyear', getCurrentFinancialYear($request->financial_year))->sum('msp_count');

            $fachiv = $user_achiv > 0 ? round((($user_achiv / 100000) * 40) / 100, 1) : 0;
            $sachiv = $user_achiv > 0 ? round((($user_achiv / 100000) * 60) / 100, 2) : 0;
            $days = ($debtors_sales / 100000) / 270 > 0 && $total_debtors > 0 ? round(($total_debtors / (($debtors_sales / 100000) / $days_difference)), 0) : '100';
            $percentage = $days <= 30 ? '100%' : ($days <= 60 ? '80%' : ($days <= 90 ? '50%' : '0%'));

            $rating_is = $this->getFR($this->getPer($user['working_days'], $working_days_trg), 5) + $this->getFR($this->getPer($user['visit_count'], $visit_count_trg), 5) + $this->getFR($this->getPer($unique_visit_count, $unique_visit_count_trg), 5) + $this->getFR($this->getPer($user_achiv / 100000, $user_target), 40) + $this->getFR($this->getPer($user_achiv_new_dealer / 100000, $fachiv), 10) + $this->getFR($this->getPer($user_achiv_new_product / 100000, $sachiv), 5) + (20 * (int)$percentage) / 100 + $this->getFR($this->getPer($active_customer, $active_customer_trg), 5) + $this->getFR($this->getPer($msp_activitys, $msp_activity_trg), 5);


            $popup_data = array();

            $popup_data['name'] = $user->name;
            $popup_data['branch'] = $user->getbranch ? $user->getbranch->branch_name : '-';
            $popup_data['designation'] = $user->getdesignation ? $user->getdesignation->designation_name : '-';
            $popup_data['rating'] = $rating_is;
            $popup_data['company_tenure'] = $user->userinfo ? $user->userinfo->current_company_tenture : '-';
            $popup_data['gross_salary'] = $user->userinfo ? $user->userinfo->gross_salary_monthly : '-';
            $popup_data['last_year_inc_value'] = $user->userinfo ? $user->userinfo->last_year_increments : '-';
            $popup_data['last_year_inc_per'] = $user->userinfo ? $user->userinfo->last_year_increment_percent : '-';
            $popup_data['target'] = $user_target;
            $popup_data['sale'] = round(($user_achiv / 100000), 2);
            $popup_data['sale_per'] = $this->getPer($user_achiv / 100000, $user_target);

            return response()->json(['status' => 'success', 'data' => $popup_data]);
        } else if (empty(array_diff($request->role_id, [3, 6, 13]))) {


            $startDate = Carbon::parse($start_date);
            $endDate = Carbon::parse($end_date);
            $monthCount = $startDate->diffInMonths($endDate) + 1;
            $selectedmonths = [];
            while ($startDate->lessThanOrEqualTo($endDate)) {
                $selectedmonths[] = $startDate->format('M');
                $startDate->addMonth();
            }

            $lastyrstartdate = Carbon::createFromFormat('Y-m-d', $start_date)->subYear()->format('Y-m-d');
            $lastyrenddate = Carbon::createFromFormat('Y-m-d', $end_date)->subYear()->format('Y-m-d');

            $user_ids = getUsersReportingToAuth($user->id);
            $emp_codes = User::whereIn('id', $user_ids)->pluck('employee_codes');
            $branch_ids = explode(',', $user->branch_id);

            $targets = SalesTargetUsers::with('user')->whereIn('branch_id', $branch_ids)->whereIn('month', $selectedmonths)->where('type', 'primary')->whereHas('user', function ($query) {
                $query->whereIn('division_id', ['10', '18']);
            })->sum('target');
            $achiv = PrimarySales::whereIn('branch_id', $branch_ids)->where('invoice_date', '>=', $start_date)->where('invoice_date', '<=', $end_date)->whereIn('division', ['PUMP', 'MOTOR'])->sum('net_amount') / 100000;
            $ly_targets = PrimarySales::whereIn('branch_id', $branch_ids)->where('invoice_date', '>=', $lastyrstartdate)->where('invoice_date', '<=', $lastyrenddate)->whereIn('division', ['PUMP', 'MOTOR'])->sum('net_amount') / 100000;

            $new_achiv_dealer = PrimarySales::whereIn('branch_id', $branch_ids)->where('invoice_date', '>=', $start_date)->where('invoice_date', '<=', $end_date)->where('new_dealer', 'Y')->whereIn('division', ['PUMP', 'MOTOR'])->sum('net_amount') / 100000;
            $new_achiv_product = PrimarySales::whereIn('branch_id', $branch_ids)->where('invoice_date', '>=', $start_date)->where('invoice_date', '<=', $end_date)->where('new_product', 'Y')->whereIn('division', ['PUMP', 'MOTOR'])->sum('net_amount') / 100000;

            $branch_names = Branch::whereIn('id', $branch_ids)->pluck('branch_name')->toArray();

            $debtors_start_date = $f_year_array[0] . '-04-01';
            // $debtors_end_date = $f_year_array[0] . '-12-31';
            $debtors_end_date = now()->toDateString();

            $debtors_start_date_or = Carbon::createFromFormat('Y-m-d', $f_year_array[0] . '-04-01');
            $debtors_end_date_or = now();

            $days_difference = $debtors_start_date_or->diffInDays($debtors_end_date_or);
            // $days_difference = 270;

            $debtors_sales = PrimarySales::whereIn('branch_id', $branch_ids)->where('invoice_date', '>=', $debtors_start_date)->where('invoice_date', '<=', $debtors_end_date)->whereIn('division', ['PUMP', 'MOTOR'])->sum('net_amount');
            $total_debtors = CustomerOutstanting::whereIn('branch_id', $branch_ids)->whereIn('division_id', ['10', '18'])->where('year', $f_year_array[0])->sum('amount');
            $total_inventory = BranchStock::whereIn('branch_id', $branch_ids)->whereIn('division_id', ['10', '18'])->where('year', $f_year_array[0])->sum('amount');

            $aop = $targets > 0 ? (round(($achiv / $targets) * 100, 0) >= 100 ? '25' : round((25 * ($achiv / $targets) * 100 / 100), 0)) : 0;
            $goly = $ly_targets > 0 ? (round((($achiv - $ly_targets) / $ly_targets) * 100, 0) >= 100 ? '25' : round((25 * (($achiv - $ly_targets) / $ly_targets) * 100 / 100), 0)) : 0;
            $new_chanel = ($achiv * 40) / 100 > 0 ? (round(($new_achiv_dealer / (($achiv * 40) / 100)) * 100, 0) >= 100 ? '15' : round((15 * ($new_achiv_dealer / (($achiv * 40) / 100)) * 100 / 100), 0)) : 0;
            $new_product = ($achiv * 60) / 100 > 0 ? (round(($new_achiv_product / (($achiv * 60) / 100)) * 100, 0) >= 100 ? '5' : round((5 * ($new_achiv_product / (($achiv * 60) / 100)) * 100 / 100), 0)) : 0;

            $days = ($debtors_sales / 100000) / 270 > 0 && $total_debtors > 0 ? round(($total_debtors / (($debtors_sales / 100000) / $days_difference)), 0) : '100';
            $percentage = $days <= 30 ? '100%' : ($days <= 60 ? '80%' : ($days <= 90 ? '50%' : '0%'));
            $debtor = (20 * (int)$percentage) / 100;

            $inv_days = ($debtors_sales / 100000) / 270 > 0 && $total_inventory > 0 ? round(($total_inventory / (($debtors_sales / 100000) / $days_difference)), 0) : '100';
            $percentage = $inv_days <= 30 ? '100%' : ($inv_days <= 60 ? '80%' : ($inv_days <= 90 ? '50%' : '0%'));
            $inventory = (10 * (int)$percentage) / 100;

            $rating_is = (int)$aop + (int)$goly + (int)$new_chanel + (int)$new_product + (int)$debtor + (int)$inventory;

            $popup_data = array();

            $popup_data['name'] = $user->name;
            $popup_data['branch'] = count($branch_names) > 0 ? implode(',', $branch_names) : '-';
            $popup_data['designation'] = $user->getdesignation ? $user->getdesignation->designation_name : '-';
            $popup_data['rating'] = $rating_is;
            $popup_data['company_tenure'] = $user->userinfo ? $user->userinfo->current_company_tenture : '-';
            $popup_data['gross_salary'] = $user->userinfo ? $user->userinfo->gross_salary_monthly : '-';
            $popup_data['last_year_inc_value'] = $user->userinfo ? $user->userinfo->last_year_increments : '-';
            $popup_data['last_year_inc_per'] = $user->userinfo ? $user->userinfo->last_year_increment_percent : '-';
            $popup_data['target'] = $targets;
            $popup_data['sale'] = round($achiv, 2);
            $popup_data['sale_per'] = $this->getPer($achiv, $targets);

            return response()->json(['status' => 'success', 'data' => $popup_data]);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Working on this role type user rating report !!']);
        }

        dd($request->all());
    }

    public function getPer($achiv, $trg)
    {
        return $trg > 0 ? round(($achiv / $trg) * 100, 0) : '0';
    }
    public function getFR($achivper, $tpoint)
    {
        if ($achivper >= 100) {
            return $tpoint;
        } else {
            return round(($tpoint * $achivper) / 100, 0) > 0 ? round(($tpoint * $achivper) / 100, 0) : '0';
        }
        return round(($achiv / $trg) * 100, 0);
    }

    // get srevice charge type
    public function getServiceChargeType(Request $request)
    {
        $complaint = Complaint::with(['product_details.subcategories'])->find($request->complaint_id);
        if (!isset($complaint->product_details->subcategories)) {
            return response()->json(['status' => false, 'html' => '']);
        }
        $service_charge_products = ServiceChargeProducts::where([
            'category_id' => $complaint->product_details->subcategories->service_category_id
        ])->distinct('charge_type_id')->pluck('charge_type_id');
        $service_charge_types = ServiceChargeChargeType::whereIn('id', $service_charge_products)->orWhere('id', 4)->get();
        $html = '<option value="">Select Type</option>';

        if (isset($service_charge_types)) {
            foreach ($service_charge_types as $service_charge) {
                $html = $html . '<option value=" ' . $service_charge->id . '">' . $service_charge->charge_type . '</option>';
            }
        }

        return response()->json(['status' => true, 'html' => $html]);
    }

    // get the total of complaints
    public function getCountsOfComplaints(Request $request)
    {
        $query = Complaint::query(); // Use query builder
        $filters = $request->all();
        if (isset($request->complaint_date)) {
            $date = explode(' - ', $request->complaint_date);
            if (isset($date)) {
                try {
                    $complaintDate_start = Carbon::parse($date[0])->startOfDay()->format('Y-m-d H:i:s');
                    $complaintDate_end = Carbon::parse($date[1])->endOfDay()->format('Y-m-d H:i:s');
                    $query->whereBetween('complaint_date', [$complaintDate_start, $complaintDate_end]);
                } catch (\Exception $e) {
                }
            }
        }
        if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Sub_Admin') && !Auth::user()->hasRole('Service Admin') && !Auth::user()->hasRole('CRM_Support')) {
            $query->where('assign_user', Auth::user()->id);
        }
        foreach ($filters as $key => $value) {
            if (isset($value)) {
                switch ($key) {
                    case 'complaint_number':
                    case 'seller':
                    case 'service_type':
                    case 'service_type_1':
                    case 'warranty_bill':
                    case 'customer_bill_no':
                    case 'under_warranty':
                    case 'company_sale_bill_no':
                    case 'register_by':
                    case 'description':
                        $query->where($key, 'like', "%$value%");
                        break;

                    case 'status':
                        $query->where('complaint_status', $value);
                        break;

                    case 'customer_complaint_type':
                        $query->whereHas('complaint_type_details', function ($q) use ($value) {
                            $q->where('name', 'like', "%$value%");
                        });
                        break;

                    case 'service_status':
                        $query->whereHas('service_bill', function ($q) use ($value) {
                            $q->where('status', $value);
                        });
                        break;

                    case 'service_branch':
                        $query->whereHas('purchased_branch_details', function ($q) use ($value) {
                            $q->whereRaw("CONCAT(branch_code, ' ', branch_name) LIKE ?", ["%$value%"]);
                        });
                        break;

                    case 'purchased_party_name':
                        $query->whereHas('customer', function ($q) use ($value) {
                            $q->whereRaw("CONCAT(customer_name, ' ', customer_number) LIKE ?", ["%$value%"]);
                        });
                        break;

                    case 'createdbyname_name':
                        $query->whereHas('createdbyname', function ($q) use ($value) {
                            $q->where('name', $value);
                        });
                        break;

                    case 'customer_bill_date':
                    case 'customer_bill_date_1':
                    case 'company_sale_bill_date':
                    case 'last_update_date':
                    case 'created_at':
                        try {
                            $formattedDate = Carbon::parse($value)->format('Y-m-d');
                            $query->whereDate($key, '=', $formattedDate);
                        } catch (\Exception $e) {
                            // Handle invalid date formats gracefully
                        }
                        break;

                    case 'service_center_name':
                        if (!empty($value)) {
                            $serviceCenterIds = is_array($value[0])
                                ? $value
                                : explode(',', $value[0]);
                            if (collect($serviceCenterIds)->filter()->isNotEmpty()) {
                                $query->whereIn('service_center', $serviceCenterIds);
                            }
                        }
                        break;

                    case 'assign_user':
                        if (!empty($value)) {
                            $assign_user_ids = is_array($value[0])
                                ? $value
                                : explode(',', $value[0]);
                            if (collect($assign_user_ids)->filter()->isNotEmpty()) {
                                $query->whereIn('assign_user', $assign_user_ids);
                            }
                        }
                        break;

                    case 'service_center_code':
                        $query->whereHas('service_center_details', function ($q) use ($value) {
                            $q->where('customer_code', 'like', "%$value%");
                        });
                        break;

                    case 'customer_name':
                    case 'customer_email':
                    case 'customer_number':
                    case 'customer_address':
                    case 'customer_place':
                    case 'customer_country':
                    case 'customer_state':
                    case 'customer_city':
                        $query->whereHas('customer', function ($q) use ($key, $value) {
                            $q->where($key, 'like', "%$value%");
                        });
                        break;

                    case 'pincode':
                        $query->whereHas('customer.pincodeDetails', function ($q) use ($value) {
                            $q->where('pincode', 'like', "%$value%");
                        });
                        break;

                    case 'category_name':
                    case 'category_name_1':
                        $query->whereHas('product_details.categories', function ($q) use ($value) {
                            $q->where('category_name', 'like', "%$value%");
                        });
                        break;

                    case 'product_name':
                    case 'product_code':
                    case 'product_serail_number':
                    case 'specification':
                    case 'product_no':
                    case 'phase':
                        $query->whereHas('product_details', function ($q) use ($key, $value) {
                            $q->where($key, 'like', "%$value%");
                        });
                        break;
                }
            }
        }
        return response()->json([
            'all_complaints'    => (clone $query)->count(),
            'complaints_pending'    => (clone $query)->where('complaint_status', '1')->count(),
            'complaints_work_done'  => (clone $query)->where('complaint_status', '2')->count(),
            'complaints_cancelled'  => (clone $query)->where('complaint_status', '5')->count(),
            'complaints_in_process' => (clone $query)->where('complaint_status', '0')->count(),
            'complaints_complete'   => (clone $query)->where('complaint_status', '3')->count(),
            'complaints_closed'     => (clone $query)->where('complaint_status', '4')->count(),
        ]);
    }

    // function for get sale data of orders
    public function getSaledata(Request $request)
    {
        $product_id = $request->product_id ?? '';
        $date = $request->date ?? '';
        $branch_id = $request->branch_id ?? '';

        if (isset($date)) {
            $now = Carbon::createFromFormat('F Y', $request->date)->startOfMonth()->subMonth();
        } else {
            $now = Carbon::now();
        }
        $lastMonth = $now->subMonth()->format('Y-m'); // Last month
        $threeMonthsAgo = $now->subMonths(3)->format('Y-m'); // Three months ago
        $lastYearSameMonth = Carbon::now()->subYear()->format('Y-m'); // Same month last year

        $last_month_sale = OrderDetails::where('product_id', $product_id)
            ->whereYear('created_at', Carbon::now()->subMonth()->year)
            ->whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereHas('orders.createdByName', function ($query) use ($branch_id) {
                $query->where('branch_id', $branch_id);
            })
            ->sum('quantity'); // Change 'quantity' based on what you sum (e.g., total_price)

        // Get last three months' average sales (excluding current month)
        $last_three_month_avg = OrderDetails::where('product_id', $product_id)
            ->whereBetween('created_at', [
                Carbon::now()->subMonths(4)->startOfMonth(), // 4 months ago (excluding current month)
                Carbon::now()->subMonth()->endOfMonth() // End of last month
            ])
            ->whereHas('orders.createdByName', function ($query) use ($branch_id) {
                $query->where('branch_id', $branch_id);
            })
            ->sum('quantity') / 3; // Divide by 3 for average

        // Get last year's same month sales
        $last_year_same_month = OrderDetails::where('product_id', $product_id)
            ->whereYear('created_at', Carbon::now()->subYear()->year)
            ->whereMonth('created_at', Carbon::now()->subYear()->month)
            ->whereHas('orders.createdByName', function ($query) use ($branch_id) {
                $query->where('branch_id', $branch_id);
            })
            ->sum('quantity');

        return response()->json([
            'last_month_sale' => $last_month_sale,
            'last_three_month_avg' => round($last_three_month_avg, 2),
            'last_year_same_month' => $last_year_same_month,
        ]);
    }

    // get service bill reason
    public function getServiceBillReason(Request $request)
    {
        $complaint_type = $request->complaint_type ?? '';
        $complaintId = $request->complaintId ?? '';
        $sub_category_id = $request->sub_category_id;
        $selected = $request->selected ?? '';
        $reasons = ServiceComplaintReason::where('service_bill_complaint_id', $complaintId)->get();
        $html = '<option value="">Select Reasons</option>';
        if (count($reasons) > 0) {
            foreach ($reasons as $reason) {
                $selected_text = $selected == $reason->service_complaint_reasons ? 'selected' : '';
                $html .= '<option value="' . htmlspecialchars($reason->service_complaint_reasons, ENT_QUOTES, 'UTF-8') . '" ' . $selected_text . '>' .
                    htmlspecialchars($reason->service_complaint_reasons ?? '', ENT_QUOTES, 'UTF-8') .
                    '</option>';
            }
        }
        return response()->json(['status' => true, 'html' => $html]);
    }

    // check complaint type and reason is allready exist or not 
    public function checkServiceBillComplaintType(Request $request)
    {
        $sub_category_id = $request->sub_category_id ?? '';
        $complaint_type = $request->complaint_type ?? '';

        $serviceBillComplaintType = ServiceBillComplaintType::where('service_bill_complaint_type_name', $complaint_type)->get() ?? '';
        if (isset($sub_category_id) && isset($serviceBillComplaintType)) {
            $find = '';
            foreach ($serviceBillComplaintType as $serviceBillComplaint) {
                $find = ServiceGroupComplaint::where(['subcategory_id' => $sub_category_id, 'service_bill_complaint_id' => $serviceBillComplaint->id])->first() ?? '';
                if (isset($find) && $find != '') {
                    break;
                }
            }
            if (isset($find) && $find != '') {
                $id = $find->service_bill_complaint_id ?? '';
                $url = route('service-bills-complaints-type.edit', encrypt($id));
                return response()->json(['status' => true, 'url' => $url]);
            }
        }
        return response()->json(['status' => false, 'url' => '']);
    }

    public function getplannedForCast(Request $request)
    {
        $division_id = $request->division_id ?? '';
        $planSOP = 0;
        if (!empty($division_id)) {
            $planSOP = PlannedSOP::where('division_id', $division_id)->sum('plan_next_month_value');
            return response()->json(['status' => true, 'total_value' => $planSOP]);
        }
        $planSOP = PlannedSOP::sum('plan_next_month_value');
        return response()->json(['status' => false, 'total_value' => $planSOP]);
    }

    public function getCustomerAddress(Request $request)
    {
        $customer_id = $request->customer_id ?? '';
        $customer = Customers::where('id', $customer_id)->first();
        if($customer && !empty($customer)){
            $data['same_address'] = $customer->same_address;
            $data['billing_address'] = $customer->customeraddress;
            $data['shipping_address'] = $customer->customershippingaddress;
            $data['phone'] = $customer->mobile;
            $data['gstin_no'] = $customer->customerdetails?->gstin_no;
            return response()->json(['status' => true, 'data' => $data]);
        }else{
            return response()->json(['status' => false]);
        }
    }
}
