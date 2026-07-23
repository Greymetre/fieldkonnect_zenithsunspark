<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SalesOrderRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'order_type' => 'required|in:b2b,b2c',
            'customer_id' => 'required|integer|exists:customers,id',
            'warehouse_id' => 'required|integer|exists:ware_houses,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|distinct|exists:products,id',
            'items.*.quantity' => 'required|numeric|gt:0',
            'items.*.rate' => 'required|numeric|min:0',
            'items.*.gst_percent' => 'required|numeric|min:0|max:100',
            'notes' => 'nullable|string|max:2000',
        ];
    }
}
