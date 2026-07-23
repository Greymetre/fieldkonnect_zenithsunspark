<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;
use Gate;

class VisitTypeRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('visittype_create') || Gate::denies('visittype_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return true;

    }

    public function rules()
    {
        $rules = [];
        switch($this) {
            case !empty($this->id) :
                $rules = [
                    'type_name'      => 'required|min:2|max:100|string|regex:/[a-zA-Z0-9\s]+/',
                ];
                break;
            default :
                $rules = [
                    'type_name'      => 'required|min:2|max:100|string|regex:/[a-zA-Z0-9\s]+/',
                ];
                break;
        }
        return $rules;
    }
}
