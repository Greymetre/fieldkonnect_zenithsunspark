<?php

namespace App\DataTables;

use App\Models\Attachment;
use App\Models\CustomerDetails;
use App\Models\Customers;
use App\Models\Gifts;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

use Illuminate\Support\Facades\Auth;

class CustomerKycDataTable extends DataTable
{

    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->editColumn('updated_at', function ($data) {
                return isset($data->updated_at) ? showdatetimeformat($data->updated_at) : '';
            })

            ->editColumn('customer.name', function ($query) {
                return '<a href="' . url("customers/" . encrypt($query->customer_id)) . '?kyc=true" target="_blank">' . $query->customer->name . '</a>';
            })
            ->editColumn('customer.customertypes.customertype_name', function ($query) {
                return $query->customer->customertypes->customertype_name;
            })
            ->editColumn('customer.first_name', function ($query) {
                return $query->customer->first_name . ' ' . $query->customer->last_name;
            })
            ->editColumn('customer.mobile', function ($query) {
                return $query->customer->mobile;
            })
            ->editColumn('customer.customeraddress.statename.state_name', function ($query) {
                return $query->customer->customeraddress ? ($query->customer->customeraddress->statename ? $query->customer->customeraddress->statename->state_name : '') : '';
            })
            ->editColumn('customer.customeraddress.districtname.district_name', function ($query) {
                return $query->customer->customeraddress ? ($query->customer->customeraddress->districtname ? $query->customer->customeraddress->districtname->district_name : '') : '';
            })
            ->editColumn('customer.customeraddress.cityname.city_name', function ($query) {
                return $query->customer->customeraddress ? ($query->customer->customeraddress->cityname ? $query->customer->customeraddress->cityname->city_name : '') : '';
            })
            ->editColumn('user_name', function ($query) {
                $employee = array();

                if (!empty($query->customer->getemployeedetail)) {
                    foreach ($query->customer->getemployeedetail as $key_new => $datas) {
                        $employee[] = isset($datas->employee_detail->name) ? $datas->employee_detail->name : '';
                    }
                }
                if (count($employee) > 0) {
                    return implode(',', $employee);
                } else {
                    return '';
                }
            })
            ->editColumn('status', function ($query) {
                $profile_image = Customers::where('id', $query->customer_id)->value('profile_image');
                $shop_image = Customers::where('id', $query->customer_id)->value('shop_image');
                $aadharback = Attachment::where('customer_id', $query->customer_id)->where('document_name', 'aadharback')->first();
                $aadhar = Attachment::where('customer_id', $query->customer_id)->where('document_name', 'aadhar')->first();

                if ($profile_image && $shop_image && $profile_image != NULL && $shop_image != NULL && $aadhar && $aadharback && $query->aadhar_no && $query->aadhar_no != null && $query->aadhar_no != '' && $query->aadhar_no_status == '0') {
                    return 'Submited';
                } elseif ($profile_image && $shop_image && $profile_image != NULL && $shop_image != NULL && $aadhar && $aadharback && $query->aadhar_no && $query->aadhar_no != null && $query->aadhar_no != '' && $query->aadhar_no_status == '1') {
                    return 'Approved';
                } elseif ($query->aadhar_no_status == '2' || $query->gstin_no_status == '2' || $query->pan_no_status == '2') {
                    return 'Reject';
                } else {
                    return 'Incomplete';
                }
            })
            ->rawColumns(['updated_at', 'customer.name', 'customer.customertypes.customertype_name', 'customer.first_name', 'customer.mobile', 'state', 'district', 'city', 'user_name', 'status']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Gift $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Gifts $model, Request $request)
    {
        $data = CustomerDetails::with(['customer', 'customer.customeraddress', 'customer.customertypes', 'customer.customeraddress.statename', 'customer.customeraddress.districtname', 'customer.customeraddress.cityname']);
        if ($request->branch_id && $request->branch_id != null && $request->branch_id != '') {
            $branch_user_id = User::where('branch_id', $request['branch_id'])->pluck('id');
            if (!empty($branch_user_id)) {
                $branch_customer_id = Customers::whereIn('executive_id', $branch_user_id)->pluck('id');
            }
            if (!empty($branch_customer_id)) {
                $data->whereIn('customer_id', $branch_customer_id);
            }
        }
        if ($request->kyc_status != null && $request->kyc_status != '') {
            if ($request->kyc_status == '5') {
                $data->whereNull('aadhar_no')->orWhere('aadhar_no', '');
            } elseif ($request->kyc_status == '0') {
                $customer_ids = Customers::whereHas('customerdocuments', function ($query) {
                    $query->whereIn('document_name', ['aadharback', 'aadhar']);
                }, '=', 2)
                ->where('profile_image','!=', NULL)
                ->where('shop_image','!=', NULL)
                // ->toSql();
                ->pluck('id');
                // dd($customer_ids);
                $data->whereIn('customer_id', $customer_ids)->where('aadhar_no_status', '0')->whereNotNull('aadhar_no')->where('aadhar_no', '!=', '');
            } else {
                $data->where('aadhar_no_status', $request->kyc_status);
            }
        }
        if ($request->customer_type && $request->customer_type != null && $request->customer_type != '') {
            $data->whereHas('customer', function ($query) use ($request) {
                $query->where('customertype', $request->customer_type);
            });
        }
        if ($request->start_date && $request->start_date != null && $request->start_date != '' && $request->end_date && $request->end_date != null && $request->end_date != '') {
            $startDate = date('Y-m-d', strtotime($request->start_date));
            $endDate = date('Y-m-d', strtotime($request->end_date));
            $data = $data->whereDate('updated_at', '>=', $startDate)
                ->whereDate('updated_at', '<=', $endDate);
        }
        $data = $data->latest()->newQuery();


        return $data;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->setTableId('gifts-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Bfrtip')
            ->orderBy(1)
            ->buttons(
                Button::make('create'),
                Button::make('export'),
                Button::make('print'),
                Button::make('reset'),
                Button::make('reload')
            );
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ->addClass('text-center'),
            Column::make('id'),
            Column::make('add your columns'),
            Column::make('created_at'),
            Column::make('updated_at'),
        ];
    }
}
