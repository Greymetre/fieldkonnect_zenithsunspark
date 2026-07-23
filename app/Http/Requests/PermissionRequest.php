<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;
use Gate;

class PermissionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        abort_if(Gate::denies('permission_create') || Gate::denies('permission_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return true;
    }

    public function rules()
    {
        $rules = [];
        switch($this->method()) {
            case "POST" :
                $rules = [
                    'name'      => 'required|min:2|max:100|string|regex:/[a-zA-Z0-9\s]+/',
                ];
                break;
            case "PUT":
                $rules = [
                    'name'      => 'required|min:2|max:200|string|regex:/[a-zA-Z0-9\s]+/',
                ];
                break;
        }
        return $rules;
    }
}
