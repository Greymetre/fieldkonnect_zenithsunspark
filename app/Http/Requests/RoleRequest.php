<?php

namespace App\Http\Requests;

use App\Role;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class RoleRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('role_create') || Gate::denies('role_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return true;
    }

    public function rules()
    {
        $rules = [];
        switch($this->method()) {
            case "POST" :
                $rules = [
                    'name'      => 'required|min:2|max:100|string|regex:/[a-zA-Z0-9\s]+/',
                    'permissions.*' => ['integer'],
                    'permissions'   => ['required','array'],
                ];
                break;
            case "PUT":
                $rules = [
                    'name'      => 'required|min:2|max:100|string|regex:/[a-zA-Z0-9\s]+/',
                    'permissions.*' => ['integer'],
                    'permissions'   => ['required','array'],
                ];
                break;
        }
        return $rules;
    }
}
