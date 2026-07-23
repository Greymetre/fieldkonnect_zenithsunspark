<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;
use Gate;

class TaskRequest extends FormRequest
{
    public function authorize()
    {
        //abort_if(Gate::denies('task_create') || Gate::denies('task_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return true;

    }

    public function rules()
    {
        $rules = [];
        switch($this) {
            case !empty($this->id) :
                $rules = [
                    'user_id'       => 'nullable|numeric|exists:users,id',
                    'title'    => 'required|max:200|string|regex:/[a-zA-Z0-9\s]+/',
                    'descriptions'  => 'required|max:400|string|regex:/[a-zA-Z0-9\s]+/',
                    // 'datetime'    => 'nullable|date_format:Y-m-d H:i:s',
                    // 'reminder'      => 'nullable|date_format:Y-m-d H:i:s',
                    'status_id'     => 'nullable|numeric|exists:statuses,id',
                ];
                break;
            default :
                $rules = [
                    'user_id'       => 'nullable|numeric|exists:users,id',
                    'title'    => 'required|max:200|string|regex:/[a-zA-Z0-9\s]+/',
                    'descriptions'  => 'required|max:400|string|regex:/[a-zA-Z0-9\s]+/',
                    // 'datetime'    => 'nullable|date_format:Y-m-d H:i:s',
                    // 'reminder'      => 'nullable|date_format:Y-m-d H:i:s',
                    'customer_id'   => 'nullable|numeric|exists:customers,id',
                    'status_id'     => 'nullable|numeric|exists:statuses,id',
                ];
                break;
        }
        return $rules;
    }
}
