<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;
use Gate;

class SchemeRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('scheme_create') || Gate::denies('scheme_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return true;

    }

    public function rules()
    {
        $rules = [];
        switch($this) {
            case !empty($this->id) :
                $rules = [
                    'scheme_name'   => 'required|min:2|max:200|string|regex:/[a-zA-Z0-9\s]+/',
                    'scheme_description'=> 'required|min:2|max:400|string|regex:/[a-zA-Z0-9\s]+/',
                    'start_date'    => 'required|date_format:Y-m-d',
                    'end_date'      => 'required|date_format:Y-m-d',
                    'points_start_date'  => 'nullable|date_format:Y-m-d',
                    'points_end_date'=> 'nullable|date_format:Y-m-d',
                    'block_points'  => 'nullable|min:1|max:100000|numeric',
                    'block_percents'=> 'nullable|min:1|max:99|numeric',
                    'scheme_type'   => 'required|min:2|max:200|string|regex:/[a-zA-Z0-9\s]+/',
                    'point_value'   => 'nullable|numeric',
                ];
                break;
            default :
                $rules = [
                    'scheme_name'   => 'required|min:2|max:200|string|regex:/[a-zA-Z0-9\s]+/',
                    'scheme_description'=> 'required|min:2|max:400|string|regex:/[a-zA-Z0-9\s]+/',
                    'start_date'    => 'required|date_format:Y-m-d',
                    'end_date'      => 'required|date_format:Y-m-d',
                    'points_start_date'  => 'nullable|date_format:Y-m-d',
                    'points_end_date'=> 'nullable|date_format:Y-m-d',
                    'block_points'  => 'nullable|min:1|max:100000|numeric',
                    'block_percents'=> 'nullable|min:1|max:99|numeric',
                    'scheme_type'   => 'required|min:2|max:200|string|regex:/[a-zA-Z0-9\s]+/',
                    'point_value'   => 'nullable|numeric',
                ];
                break;
        }
        return $rules;
    }
}
