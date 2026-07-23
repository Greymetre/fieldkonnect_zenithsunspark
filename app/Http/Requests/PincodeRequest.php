<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;
use Gate;

class PincodeRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('pincode_create') || Gate::denies('pincode_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return true;
    }

    public function rules()
    {
        $rules = [];
        switch($this) {
            case !empty($this->id) :
                $rules = [
                    'pincode'   =>  'required|min:2|max:100|string|regex:/[a-zA-Z0-9\s]+/',
                    'city_id'   =>  'required|numeric|exists:cities,id',
                ];
                break;
            default :
                $rules = [
                    'pincode'   =>  'required|min:2|max:100|string|regex:/[a-zA-Z0-9\s]+/',
                    'city_id'   =>  'required|numeric|exists:cities,id',
                ];
                break;
        }
        return $rules;
    }
}
