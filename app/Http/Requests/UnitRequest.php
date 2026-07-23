<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;
use Gate;

class UnitRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('unit_create') || Gate::denies('unit_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return true;

    }

    public function rules()
    {
        $rules = [];
        switch($this) {
            case !empty($this->id) :
                $rules = [
                    'unit_name'      => 'required|min:2|max:100|string|regex:/[a-zA-Z0-9\s]+/',
                    'unit_code'    => 'required|min:2|max:100|string|regex:/[a-zA-Z0-9\s]+/',
                ];
                break;
            default :
                $rules = [
                    'unit_name'      => 'required|min:2|max:200|string|regex:/[a-zA-Z0-9\s]+/',
                    'unit_code'    => 'required|min:2|max:200|string|regex:/[a-zA-Z0-9\s]+/',
                ];
                break;
        }
        return $rules;
    }
}
