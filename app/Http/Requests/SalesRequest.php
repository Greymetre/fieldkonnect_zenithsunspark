<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;
use Gate;

class SalesRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('sale_create') || Gate::denies('sale_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return true;

    }

    public function rules()
    {
        $rules = [];
        switch($this) {
            case !empty($this->id) :
                $rules = [
                    'buyer_id'      => 'required|numeric|exists:customers,id',
                    'seller_id'     => 'required|numeric|exists:customers,id',
                    'order_id'      => 'nullable|numeric|exists:orders,id',
                    'order_no'      => 'nullable|min:2|max:100|string|regex:/[a-zA-Z0-9\s]+/',
                    'fiscal_year'   => 'nullable|min:2|max:50|string|regex:/[a-zA-Z0-9\s]+/',
                    'sales_no'      => 'nullable|min:2|max:100|string|regex:/[a-zA-Z0-9\s]+/',
                    'invoice_no'    => 'required|min:2|max:100|string|regex:/[a-zA-Z0-9\s]+/',
                    'invoice_date'  => 'required|date_format:Y-m-d',
                    'total_discount'=> 'nullable|numeric',
                    'extra_discount'=> 'nullable|numeric',
                    'extra_discount_amount'   => 'nullable|numeric',
                    'total_gst'     => 'nullable|numeric',
                    'total_qty'     => 'nullable|numeric',
                    'shipped_qty'   => 'nullable|numeric',
                    'sub_total'     => 'required|numeric',
                    'grand_total'   => 'required|numeric',
                    'description'   => 'nullable|min:2|max:400|string|regex:/[a-zA-Z0-9\s]+/',
                    'status_id'     => 'nullable|numeric|exists:statuses,id',
                ];
                break;
            default :
                $rules = [
                    'buyer_id'      => 'required|numeric|exists:customers,id',
                    'seller_id'     => 'required|numeric|exists:customers,id',
                    'order_id'      => 'nullable|numeric|exists:orders,id',
                    'order_no'      => 'nullable|min:2|max:100|string|regex:/[a-zA-Z0-9\s]+/',
                    'fiscal_year'   => 'nullable|min:2|max:50|string|regex:/[a-zA-Z0-9\s]+/',
                    'sales_no'      => 'nullable|min:2|max:100|string|regex:/[a-zA-Z0-9\s]+/',
                    'invoice_no'    => 'required|min:2|max:100|string|regex:/[a-zA-Z0-9\s]+/',
                    'invoice_date'  => 'required|date_format:Y-m-d',
                    'total_discount'=> 'nullable|numeric',
                    'extra_discount'=> 'nullable|numeric',
                    'extra_discount_amount'   => 'nullable|numeric',
                    'total_gst'     => 'nullable|numeric',
                    'total_qty'     => 'nullable|numeric',
                    'shipped_qty'   => 'nullable|numeric',
                    'sub_total'     => 'required|numeric',
                    'grand_total'   => 'required|numeric',
                    'description'   => 'nullable|min:2|max:400|string|regex:/[a-zA-Z0-9\s]+/',
                    'status_id'     => 'nullable|numeric|exists:statuses,id',
                ];
                break;
        }
        return $rules;
    }
}
