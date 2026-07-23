<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;
use Gate;

class StatusRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('status_create') || Gate::denies('status_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return true;

    }

    public function rules()
    {
        $rules = [];
        switch($this) {
            case !empty($this->id) :
                $rules = [
                    'status_name'   => 'required|min:2|max:100|string|regex:/[a-zA-Z0-9\s]+/',
                    'display_name'  => 'required|min:2|max:100|string|regex:/[a-zA-Z0-9\s]+/',
                    'status_message'=> 'required|min:2|max:100|string|regex:/[a-zA-Z0-9\s]+/',
                    'module'        => 'nullable|min:2|max:100|string|regex:/[a-zA-Z0-9\s]+/',
                ];
                break;
            default :
                $rules = [
                    'status_name'   => 'required|min:2|max:200|string|regex:/[a-zA-Z0-9\s]+/',
                    'display_name'  => 'required|min:2|max:200|string|regex:/[a-zA-Z0-9\s]+/',
                    'status_message'=> 'required|min:2|max:200|string|regex:/[a-zA-Z0-9\s]+/',
                    'module'        => 'nullable|min:2|max:200|string|regex:/[a-zA-Z0-9\s]+/',
                ];
                break;
        }
        return $rules;
    }
}
