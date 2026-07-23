<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;
use Gate;

class GiftModelRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('subcategory_create') || Gate::denies('subcategory_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;

    }

    public function rules()
    {
        $rules = [];
        switch($this) {
            case !empty($this->id) :
                $rules = [
                    'model_name'   => 'required|min:2|max:100|string|regex:/[a-zA-Z0-9\s]+/',
                    'image'        => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                    'sub_category_id'     => 'required|numeric|exists:giftsubcategories,id',
                    
                ];
                break;
            default :
                $rules = [
                    'model_name'   => 'required|min:2|max:100|string|regex:/[a-zA-Z0-9\s]+/',
                    'image'        => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                    'sub_category_id'     => 'required|numeric|exists:giftsubcategories,id',
                ];
                break;
        }
        return $rules;
    }
}
