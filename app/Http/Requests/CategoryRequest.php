<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;
use Gate;

class CategoryRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('category_create')||Gate::denies('category_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return true;
    }

    public function rules()
    {
        $rules = [];
        switch($this) {
            case !empty($this->id) :
                $rules = [
                    'category_name'    => 'required|min:2|max:100|string|regex:/[a-zA-Z0-9\s]+/',
                    'image'         => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                ];
                break;
            default:
                $rules = [
                    'category_name'    => 'required|min:2|max:100|string|regex:/[a-zA-Z0-9\s]+/',
                    'image'         => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                ];
                break;
        }
        return $rules;
    }
}
