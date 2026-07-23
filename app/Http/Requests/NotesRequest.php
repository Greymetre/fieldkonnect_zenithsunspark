<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;
use Gate;

class NotesRequest extends FormRequest
{
    public function authorize()
    {
        //abort_if(Gate::denies('note_create') || Gate::denies('note_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return true;
    }

    public function rules()
    {
        $rules = [];
        switch($this) {
            case !empty($this->id) :
                $rules = [
                    'note'      => 'required|min:2|max:1000|string|regex:/[a-zA-Z0-9\s]+/',
                    'user_id'   =>  'nullable|numeric|exists:users,id',
                    'status_id' =>  'nullable|numeric|exists:statuses,id',
                ];
                break;
            default :
                $rules = [
                    'note'      => 'required|min:2|max:1000|string|regex:/[a-zA-Z0-9\s]+/',
                    'user_id'   =>  'nullable|numeric|exists:users,id',
                    'status_id' =>  'nullable|numeric|exists:statuses,id',
                ];
                break;
        }
        return $rules;
    }
}
