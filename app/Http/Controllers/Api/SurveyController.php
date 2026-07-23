<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


use Validator;
use Gate;
use App\Models\Field;
use App\Models\Customers;
use App\Models\SurveyData;

class SurveyController extends Controller
{
    public function __construct()
    {
        $this->field = new Field();
        
        $this->successStatus = 200;
        $this->created = 201;
        $this->accepted = 202;
        $this->noContent = 204;
        $this->badrequest = 400;
        $this->unauthorized = 401;
        $this->notFound = 404;
        $this->notactive = 406;
        $this->internalError = 500;
    }

    public function getSurveyQuestions(Request $request)
    {
        try
        { 
            $user = $request->user();

            $user_id = $user->id;
            $division_id = $user->department_id;

            $pageSize = $request->input('pageSize');
            $customertype = $request->input('customer_type');
            $customer_id = $request->input('customer_id');
            $customersurvey = SurveyData::where('customer_id','=',$customer_id)->select('field_id','value')->get();

            $query = Field::with('fieldsData')
                            ->where(function ($query) use($customertype,$division_id) {
                                if(!empty($customertype))
                                {
                                    $query->where('module', '=',$customertype);
                                }

                                if(!empty($division_id))
                                {
                                    $query->where('division_id', '=',$division_id);
                                }


                                $query->where('active', '=','Y');
                            })
                            ->select('id','field_name','field_type','label_name','placeholder','is_required','is_multiple')
                            ->orderBy('ranking', 'ASC');

            $db_data = (!empty($pageSize)) ? $query->paginate($pageSize) : $query->get();
            $data = collect([]);
            if($db_data->isNotEmpty())
            {
                foreach ($db_data as $key => $value) {
                    $details = collect([]);
                    if(!empty($value['fieldsData']))
                    {
                        foreach ($value['fieldsData'] as $key => $rows) {
                            $details->push([
                                'value_id' => isset($rows['id']) ? $rows['id'] : 0,
                                'value' => isset($rows['value']) ? $rows['value'] : '',
                            ]);
                        }
                    }
                    $oldans = $customersurvey->where('field_id','=',$value['id'])->first();
                    $data->push([
                        'field_id' => isset($value['id']) ? $value['id'] : 0,
                        'field_name' => isset($value['field_name']) ? $value['field_name'] : '',
                        'field_type' => isset($value['field_type']) ? $value['field_type'] : '',
                        'label_name' => isset($value['label_name']) ? $value['label_name'] : '',
                        'placeholder' => isset($value['placeholder']) ? $value['placeholder'] : '',
                        'is_required' => isset($value['is_required']) ? $value['is_required'] : '',
                        'is_multiple' => isset($value['is_multiple']) ? $value['is_multiple'] : '',
                        'value' => isset($oldans['value']) ? $oldans['value'] : '',
                        'field_data'  => $details
                    ]);
                }
                return response()->json(['status' => 'success','message' => 'Data retrieved successfully.','data' => $data ], $this->successStatus);
            }
            return response(['status' => 'error', 'message' => 'No Record Found.', 'data' => $data ],200);  
            
        }
        catch(\Exception $e)
        {
            return response()->json(['status' => 'error','message' => $e->getMessage() ], $this->internalError);
        }        
    }
}
