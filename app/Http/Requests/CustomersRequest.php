<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;
use Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CustomersRequest extends FormRequest
{
    protected function prepareForValidation()
    {
        if (!$this->filled('customertype')) {
            $this->merge(['customertype' => 1]);
        }
    }

    public function authorize()
    {
        $ability = !empty($this->id) ? 'customer_edit' : 'customer_create';
        abort_if(Gate::denies($ability), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return true;
    }

    public function rules()
    {
        $rules = []; 
        switch($this) {
            case !empty($this->id) :
                $rules = [
                    'name'          => 'required|min:2|max:100|string|regex:/[a-zA-Z0-9\s]+/',
                    'first_name'    => 'nullable|min:2|max:100|string|regex:/[a-zA-Z0-9\s]+/',
                    'last_name'     => 'nullable|string|regex:/[a-zA-Z0-9\s]+/',
                    'address1'      => 'required|min:2|max:250|string',
                    'shipping_address1' => 'required_unless:same_address,1|nullable|min:2|max:250|string',
                    'mobile'        => 'required|digits:10|unique:customers,mobile,NULL,id,'.$this->id,
                    'email'         => 'nullable|min:7|max:200|email|unique:customers,email,'.$this->id,
                    'customer_code' => 'nullable|max:100|string|regex:/[a-zA-Z0-9\s]+/|unique:customers,customer_code,'.$this->id,
                    'gstin_no'      => 'nullable|min:4|max:100|string',
                    'pan_no'        => 'nullable|min:4|max:100|string',
                    'aadhar_no'     => 'nullable|min:4|max:100|string',
                    'otherid_no'    => 'nullable|min:4|max:100|string',
                    'country_id'    => 'nullable|numeric|exists:countries,id',
                    'state_id'      => 'nullable|numeric|exists:states,id',
                    'district_id'   => 'nullable|numeric|exists:districts,id',
                    'city_id'       => 'nullable|numeric|exists:cities,id',
                    'pincode_id'    => 'nullable|numeric|exists:pincodes,id',
                    'status_id'     => 'nullable|numeric|exists:statuses,id',
                    'customertype'  => 'required|integer|exists:customer_types,id',
                    'firmtype'      => 'required|integer|exists:firm_types,id',
                ];
                break;
            default :
                $rules = [
                    'name'          => 'required|min:2|max:100|string|regex:/[a-zA-Z0-9\s]+/',
                    'first_name'    => 'nullable|min:2|max:100|string|regex:/[a-zA-Z0-9\s]+/',
                    'last_name'     => 'nullable|min:2|max:100|string|regex:/[a-zA-Z0-9\s]+/',
                    'address1'      => 'required|min:2|max:250|string',
                    'shipping_address1' => 'required_unless:same_address,1|nullable|min:2|max:250|string',
                    'mobile'        => 'required|digits:10|unique:customers,mobile',
                    'email'         => 'nullable|min:7|max:200|email||unique:customers,email',
                    'customer_code' => 'nullable|min:2|max:100|string|regex:/[a-zA-Z0-9\s]+/|unique:customers,customer_code',
                    'gstin_no'      => 'nullable|min:4|max:100|string',
                    'pan_no'        => 'nullable|min:4|max:100|string',
                    'aadhar_no'     => 'nullable|min:4|max:100|string',
                    'otherid_no'    => 'nullable|min:4|max:100|string',
                    'country_id'    => 'nullable|numeric|exists:countries,id',
                    'state_id'      => 'nullable|numeric|exists:states,id',
                    'district_id'   => 'nullable|numeric|exists:districts,id',
                    'city_id'       => 'nullable|numeric|exists:cities,id',
                    'pincode_id'    => 'nullable|numeric|exists:pincodes,id',
                    'status_id'     => 'nullable|numeric|exists:statuses,id',
                    'customertype'  => 'required|integer|exists:customer_types,id',
                    'firmtype'      => 'required|integer|exists:firm_types,id',
                ];
                break;
        }
        return $rules;
    }
}
