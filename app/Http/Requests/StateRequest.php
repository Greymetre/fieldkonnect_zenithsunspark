<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;
use Gate;

class StateRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('state_create') || Gate::denies('state_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return true;

    }

    public function rules()
    {
        $rules = [];
        switch($this) {
            case !empty($this->id) :
                $rules = [
                    'state_name'      => 'required|min:2|max:100|string|regex:/[a-zA-Z0-9\s]+/',
                    'country_id'     => 'required|numeric|exists:countries,id',
                ];
                break;
            default :
                $rules = [
                    'state_name'      => 'required|min:2|max:100|string|regex:/[a-zA-Z0-9\s]+/',
                    'country_id'     => 'required|numeric|exists:countries,id',
                ];
                break;
        }
        return $rules;
    }
}
