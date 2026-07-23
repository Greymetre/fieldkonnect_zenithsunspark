<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;
use Gate;

class SubcategoryRequest extends FormRequest
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
                    'subcategory_name'   => 'required|min:2|max:100',
                    'image'        => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                    'category_id'     => 'required|numeric|exists:categories,id',
                    
                ];
                break;
            default :
                $rules = [
                    'subcategory_name'   => 'required|min:2|max:100',
                    'image'        => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                    'category_id'     => 'required|numeric|exists:categories,id',
                ];
                break;
        }
        return $rules;
    }
}
