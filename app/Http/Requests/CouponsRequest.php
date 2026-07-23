<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;
use Gate;

class CouponsRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('coupon_create') || Gate::denies('coupon_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return true;
    }

    public function rules()
    {
        $rules = [];
        switch($this) {
            case !empty($this->id) :
                $rules = [
                    'profile_name'      => 'required|min:2|max:100|string|regex:/[a-zA-Z0-9\s]+/',
                    'coupon_count'    => 'required|min:1|max:100000|numeric',
                    'excluding_character'   => 'nullable|string|regex:/[a-zA-Z0-9\s]+/',
                    'coupon_length'   => 'nullable|min:2|max:100|numeric',
                ];
                break;
            default :
                $rules = [
                    'profile_name'      => 'required|min:2|max:100|string|regex:/[a-zA-Z0-9\s]+/',
                    'coupon_count'    => 'required|min:1|max:100000|numeric',
                    'excluding_character'   => 'nullable|string|regex:/[a-zA-Z0-9\s]+/',
                    'coupon_length'   => 'nullable|min:2|max:100|numeric',
                ];
                break;
        }
        return $rules;
    }
}
