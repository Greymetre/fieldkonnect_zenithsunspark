<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;
use Gate;

class SettingsRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('setting_create') || Gate::denies('setting_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return true;

    }

    public function rules()
    {
        $rules = [];
        switch($this) {
            case !empty($this->id) :
                $rules = [
                    'title'      => 'required|min:2|max:100|string|regex:/[a-zA-Z0-9\s]+/',
                    'key_name'    => 'required|min:2|max:100|string|regex:/[a-zA-Z0-9\s]+/',
                    'value'   => 'required|min:2|max:100|string|regex:/[a-zA-Z0-9\s]+/',
                    'module'   => 'required|min:2|max:100|string|regex:/[a-zA-Z0-9\s]+/',
                ];
                break;
            default :
                $rules = [
                    'title'      => 'required|min:2|max:100|string|regex:/[a-zA-Z0-9\s]+/',
                    'key_name'    => 'required|min:2|max:100|string|regex:/[a-zA-Z0-9\s]+/',
                    'value'   => 'required|min:2|max:100|string|regex:/[a-zA-Z0-9\s]+/',
                    'module'   => 'required|min:2|max:100|string|regex:/[a-zA-Z0-9\s]+/',
                ];
                break;
        }
        return $rules;
    }
}
