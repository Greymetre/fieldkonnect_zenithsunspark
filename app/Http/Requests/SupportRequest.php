<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;
use Gate;

class SupportRequest extends FormRequest
{
    public function authorize()
    {
       //abort_if(Gate::denies('supports_create') || Gate::denies('supports_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return true;

    }

    public function rules()
    {
        $rules = [
            'subject'       => 'required|max:200|string',
            'user_id'       => 'required|numeric|exists:users,id',
            'status_id'     => 'nullable|numeric|exists:statuses,id',
            'department_id' => 'nullable|numeric|exists:departments,id',
            'customer_id'   => 'nullable|numeric|exists:customers,id',
            //'full_name'     => 'required|max:100|string',
            'priority'      => 'nullable|numeric|exists:priorities,id',
        ];
        return $rules;
    }
}
