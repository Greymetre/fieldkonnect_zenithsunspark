<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;
use Gate;

class WalletRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('wallet_create') || Gate::denies('wallet_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return true;

    }

    public function rules()
    {
        $rules = [];
        switch($this) {
            case !empty($this->id) :
                $rules = [
                    'customer_id'   => 'required|numeric|exists:customers,id',
                    'scheme_id'     => 'required|numeric|exists:scheme_headers,id',
                    'schemedetail_id'=> 'nullable|numeric|exists:scheme_details,id',
                    'points'        => 'nullable|numeric',
                    'point_type'    => 'nullable|min:2|max:100|string|regex:/[a-zA-Z0-9\s]+/',
                    'invoice_amount'=> 'nullable|numeric',
                    'invoice_no'    => 'nullable|min:2|max:200|string|regex:/[a-zA-Z0-9\s]+/',
                    'coupon_code'   => 'nullable|min:2|max:200|string|regex:/[a-zA-Z0-9\s]+/|exists:coupons,coupon_code',
                    'invoice_date'  => 'required|date_format:Y-m-d',
                    'transaction_type'=> 'nullable|min:2|max:20|string|regex:/[a-zA-Z0-9\s]+/',
                    'scheme_id'     => 'nullable|numeric|exists:scheme_headers,id',
                    'sales_id'      => 'nullable|numeric|exists:sales,id',
                    'purchase_id'   => 'nullable|numeric|exists:purchases,id',
                    'status_id'     => 'nullable|numeric|exists:statuses,id',
                    
                ];
                break;
            default :
                $rules = [
                    'customer_id'   => 'required|numeric|exists:customers,id',
                    'scheme_id'     => 'required|numeric|exists:scheme_headers,id',
                    'schemedetail_id'=> 'nullable|numeric|exists:scheme_details,id',
                    'points'        => 'nullable|numeric',
                    'point_type'    => 'nullable|min:2|max:100|string|regex:/[a-zA-Z0-9\s]+/',
                    'invoice_amount'=> 'nullable|numeric',
                    'invoice_no'    => 'nullable|min:2|max:200|string|regex:/[a-zA-Z0-9\s]+/',
                    'coupon_code'   => 'nullable|min:2|max:200|string|regex:/[a-zA-Z0-9\s]+/|exists:coupons,coupon_code',
                    'invoice_date'  => 'required|date_format:Y-m-d',
                    'transaction_type'=> 'nullable|min:2|max:20|string|regex:/[a-zA-Z0-9\s]+/',
                    'scheme_id'     => 'nullable|numeric|exists:scheme_headers,id',
                    'sales_id'      => 'nullable|numeric|exists:sales,id',
                    'purchase_id'   => 'nullable|numeric|exists:purchases,id',
                    'status_id'     => 'nullable|numeric|exists:statuses,id',
                ];
                break;
        }
        return $rules;
    }
}
