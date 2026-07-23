<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;
use Gate;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        abort_if(Gate::denies('user_create') || Gate::denies('user_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return true;
    }

    public function rules()
    {
        $rules = [];
        switch($this) {
            case !empty($this->id) :
                $rules = [
                    'name'          => 'required|min:2|max:100|string|regex:/[a-zA-Z0-9\s]+/',
                    'first_name'    => 'required|min:2|max:100|string|regex:/[a-zA-Z0-9\s]+/',
                    'last_name'     => 'required|min:2|max:100|string|regex:/[a-zA-Z0-9\s]+/',
                    'address'       => 'nullable|min:2|max:100|string|regex:/[a-zA-Z0-9\s]+/',
                   'mobile'        => 'required|numeric|unique:users,mobile,'.$this->id,
                    'email'         => 'required|min:7|max:200|email||unique:users,email,'.$this->id,
                    'country_id'    => 'nullable|numeric|exists:countries,id',
                    'state_id'      => 'nullable|numeric|exists:states,id',
                    'district_id'   => 'nullable|numeric|exists:districts,id',
                    'city_id'       => 'nullable|numeric|exists:cities,id',
                    'pincode_id'    => 'nullable|numeric|exists:pincodes,id',
                    'status_id'     => 'nullable|numeric|exists:statuses,id',
                    'image'        => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                ];
                break;
            default :
                $rules = [
                    'name'          => 'required|min:2|max:100|string|regex:/[a-zA-Z0-9\s]+/',
                    'first_name'    => 'required|min:2|max:100|string|regex:/[a-zA-Z0-9\s]+/',
                    'last_name'     => 'required|min:2|max:100|string|regex:/[a-zA-Z0-9\s]+/',
                    'address'       => 'nullable|min:2|max:100|string|regex:/[a-zA-Z0-9\s]+/',
                    'mobile'        => 'required|numeric|unique:users,mobile',
                    'email'         => 'required|min:7|max:200|email||unique:users,email',
                    'country_id'    => 'nullable|numeric|exists:countries,id',
                    'state_id'      => 'nullable|numeric|exists:states,id',
                    'district_id'   => 'nullable|numeric|exists:districts,id',
                    'city_id'       => 'nullable|numeric|exists:cities,id',
                    'pincode_id'    => 'nullable|numeric|exists:pincodes,id',
                    'status_id'     => 'nullable|numeric|exists:statuses,id',
                    'image'        => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                ];
                break;
        }
        return $rules;
    }
}
