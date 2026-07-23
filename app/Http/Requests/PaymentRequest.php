<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;
use Gate;

class PaymentRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('payment_create') || Gate::denies('payment_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return true;
    }

    public function rules()
    {
        $rules = [];
        switch($this) {
            case !empty($this->id) :
                $rules = [
                    'invoice_no'       => 'nullable|min:2|max:100|string|regex:/[a-zA-Z0-9\s]+/',
                    'customer_id'      => 'nullable|numeric|exists:customers,id',
                    'sales_id'     => 'nullable|numeric|exists:sales,id',
                    'order_id'     => 'nullable|numeric|exists:orders,id',
                    'status_id'     => 'nullable|numeric|exists:statuses,id',
                    'bank_name'       => 'nullable|min:2|max:200|string|regex:/[a-zA-Z0-9\s]+/',
                    'transaction_id'       => 'nullable|min:2|max:200|string|regex:/[a-zA-Z0-9\s]+/',
                    'payment_mode'       => 'nullable|min:2|max:200|string|regex:/[a-zA-Z0-9\s]+/',
                    'banktxnid'       => 'nullable|min:2|max:200|string|regex:/[a-zA-Z0-9\s]+/',
                    'response'       => 'nullable|min:2|max:200|string|regex:/[a-zA-Z0-9\s]+/',
                    'currency'       => 'nullable|min:2|max:100|string|regex:/[a-zA-Z0-9\s]+/',
                    'payment_date'    => 'nullable|date_format:Y-m-d',
                ];
                break;
            default :
                $rules = [
                    'invoice_no'       => 'nullable|min:2|max:100|string|regex:/[a-zA-Z0-9\s]+/',
                    'customer_id'      => 'nullable|numeric|exists:customers,id',
                    'sales_id'     => 'nullable|numeric|exists:sales,id',
                    'order_id'     => 'nullable|numeric|exists:orders,id',
                    'status_id'     => 'nullable|numeric|exists:statuses,id',
                    'bank_name'       => 'nullable|min:2|max:200|string|regex:/[a-zA-Z0-9\s]+/',
                    'transaction_id'       => 'nullable|min:2|max:200|string|regex:/[a-zA-Z0-9\s]+/',
                    'payment_mode'       => 'nullable|min:2|max:200|string|regex:/[a-zA-Z0-9\s]+/',
                    'banktxnid'       => 'nullable|min:2|max:200|string|regex:/[a-zA-Z0-9\s]+/',
                    'response'       => 'nullable|min:2|max:200|string|regex:/[a-zA-Z0-9\s]+/',
                    'currency'       => 'nullable|min:2|max:100|string|regex:/[a-zA-Z0-9\s]+/',
                    'payment_date'    => 'nullable|date_format:Y-m-d',
                ];
                break;
        }
        return $rules;
    }
}
