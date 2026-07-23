<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;
use Gate;

class GiftsRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('gift_create') || Gate::denies('gift_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return true;
    }

    public function rules()
    {
        $rules = [];
        switch($this) {
            case !empty($this->id) :
                $rules = [
                    'product_name'  =>  'required',
                    'display_name'  =>  'required',
                    'description'   =>  'required',
                    'mrp'           =>  'nullable|numeric',
                    'price'         =>  'nullable|numeric',
                    'points'        =>  'nullable|numeric',
                    'subcategory_id'=>  'nullable|numeric|exists:giftsubcategories,id',
                    'category_id'   =>  'nullable|numeric|exists:gift_categories,id',
                    'brand_id'      =>  'nullable|numeric|exists:gift_brands,id',
                    'image'         =>  'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                ];
                break;
            default :
                $rules = [
                    'product_name'  =>  'required',
                    'display_name'  =>  'required',
                    'description'   =>  'required',
                    'mrp'           =>  'nullable|numeric',
                    'price'         =>  'nullable|numeric',
                    'points'        =>  'nullable|numeric',
                    'subcategory_id'=>  'nullable|numeric|exists:giftsubcategories,id',
                    'category_id'   =>  'nullable|numeric|exists:gift_categories,id',
                    'brand_id'      =>  'nullable|numeric|exists:gift_brands,id',
                    'image'         =>  'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                ];
                break;
        }
        return $rules;
    }
}
