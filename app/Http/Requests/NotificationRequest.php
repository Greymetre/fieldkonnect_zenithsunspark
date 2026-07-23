<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;
use Gate;

class NotificationRequest extends FormRequest
{
    public function authorize()
    {
        //abort_if(Gate::denies('notification_create') || Gate::denies('notification_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return true;
    }

    public function rules()
    {
        $rules = [];
         switch($this) {
            case !empty($this->id) :
                $rules = [
                    'data'      => 'required|min:2|max:1000|string|regex:/[a-zA-Z0-9\s]+/',
                    'type'      => 'nullable|min:2|max:200|string|regex:/[a-zA-Z0-9\s]+/',
                    'user_id'   =>  'nullable|numeric|exists:users,id',
                    'customer_id'   =>  'nullable|numeric|exists:customers,id',
                ];
                break;
            default :
                $rules = [
                    'data'      => 'required|min:2|max:1000|string|regex:/[a-zA-Z0-9\s]+/',
                    'type'      => 'nullable|min:2|max:200|string|regex:/[a-zA-Z0-9\s]+/',
                    'user_id'   =>  'nullable|numeric|exists:users,id',
                    'customer_id'   =>  'nullable|numeric|exists:customers,id',
                ];
                break;
        }
        return $rules;
    }
}
