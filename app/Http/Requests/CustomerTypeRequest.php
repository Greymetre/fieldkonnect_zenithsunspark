<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;
use Gate;

class CustomerTypeRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('customertype_create') || Gate::denies('customertype_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return true;
    }

    public function rules()
    {
        $rules = [];
        switch($this) {
            case !empty($this->id) :
                $rules = [
                    'customertype_name'      => 'required|min:2|max:100|string|regex:/[a-zA-Z0-9\s]+/',
                    'type_name'    => 'required|min:2|max:100|string|regex:/[a-zA-Z0-9\s]+/',
                ];
                break;
            default :
                $rules = [
                    'customertype_name'      => 'required|min:2|max:200|string|regex:/[a-zA-Z0-9\s]+/',
                    'type_name'    => 'required|min:2|max:200|string|regex:/[a-zA-Z0-9\s]+/',
                ];
                break;
        }
        return $rules;
    }
}
