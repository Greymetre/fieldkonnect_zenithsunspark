<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;
use Gate;

class DistrictRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('district_create') || Gate::denies('district_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return true;
    }

    public function rules()
    {
        $rules = [];
        switch($this) {
            case !empty($this->id) :
                $rules = [
                    'district_name' => 'required|min:2|max:100|string|regex:/[a-zA-Z0-9\s]+/',
                    'state_id'      => 'required|numeric|exists:states,id',
                ];
                break;
            default :
                $rules = [
                    'district_name'      => 'required|min:2|max:100|string|regex:/[a-zA-Z0-9\s]+/',
                    'state_id'      => 'required|numeric|exists:states,id',
                ];
                break;
        }
        return $rules;
    }
}
