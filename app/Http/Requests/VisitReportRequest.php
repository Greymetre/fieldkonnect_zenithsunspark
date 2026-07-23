<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;
use Gate;

class VisitReportRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('visitreport_create') || Gate::denies('visitreport_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;

    }

    public function rules()
    {
        $rules = [];
        switch($this) {
            case !empty($this->id) :
                $rules = [
                    'customer_id'      => 'required|numeric|exists:customers,id',
                    'user_id'     => 'required|numeric|exists:users,id',
                    'checkin_id'      => 'nullable|numeric|exists:check_in,id',
                    'visit_type_id'      => 'nullable|numeric|exists:visit_types,id',
                    'report_title'      => 'required|min:2|max:200|string|regex:/[a-zA-Z0-9\s]+/',
                    'description'    => 'required|min:2|max:300|string|regex:/[a-zA-Z0-9\s]+/',
                ];
                break;
            default :
                $rules = [
                    'customer_id'      => 'required|numeric|exists:customers,id',
                    'user_id'     => 'required|numeric|exists:users,id',
                    'checkin_id'      => 'nullable|numeric|exists:check_in,id',
                    'visit_type_id'      => 'nullable|numeric|exists:visit_types,id',
                    'report_title'      => 'required|min:2|max:200|string|regex:/[a-zA-Z0-9\s]+/',
                    'description'    => 'required|min:2|max:300|string|regex:/[a-zA-Z0-9\s]+/',
                ];
                break;
        }
        return $rules;
    }
}
