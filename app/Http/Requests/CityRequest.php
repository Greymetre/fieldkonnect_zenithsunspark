<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;
use Gate;

class CityRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('city_create') || Gate::denies('city_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return true;
    }

    public function rules()
    {
        $rules = [];
         switch($this) {
            case !empty($this->id) :
                $rules = [
                    'city_name'    => 'required|min:2|max:100|string|regex:/[a-zA-Z0-9\s]+/',
                    'district_id'    => 'required|numeric|exists:districts,id',
                ];
                break;
            default :
                $rules = [
                    'city_name'    => 'required|min:2|max:100|string|regex:/[a-zA-Z0-9\s]+/',
                    'district_id'    => 'required|numeric|exists:districts,id',
                ];
                break;
        }
        return $rules;
    }
}
