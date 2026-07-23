<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VisitReport;
use App\Models\VisitType;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Helpers\GlobalHelper;

use Validator;
use Gate;


class VisitReportController extends Controller
{
    public function __construct()
    {
        $this->visitreports = new VisitReport();
        
        
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

    public function getVisitTypes(Request $request)
    {
        try
        { 
            $user = $request->user();
            $user_id = $user->id;
            $query = VisitType::where('active','=','Y')->select('id','type_name')->latest();
            $db_data = (!empty($pageSize)) ? $query->paginate($pageSize) : $query->get();
            $data = collect([]);
            if($db_data->isNotEmpty())
            {
                foreach ($db_data as $key => $value) {
                    $data->push([
                        'type_id' => isset($value['id']) ? $value['id'] : 0,
                        'type_name' => isset($value['type_name']) ? $value['type_name'] : '',
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

    public function getVisitReports(Request $request)
    {
        try
        { 
            $user = $request->user();
            $user_id = $user->id;
            $query = VisitReport::where('user_id','=',$user_id)->select('id','checkin_id', 'user_id', 'customer_id', 'visit_type_id', 'report_title', 'description')->latest();
            $db_data = (!empty($pageSize)) ? $query->paginate($pageSize) : $query->get();
            $data = collect([]);
            if($db_data->isNotEmpty())
            {
                foreach ($db_data as $key => $value) {
                    $data->push([
                        'report_id' => isset($value['id']) ? $value['id'] : 0,
                        'checkin_id' => isset($value['checkin_id']) ? $value['checkin_id'] : 0,
                        'user_id' => isset($value['user_id']) ? $value['user_id'] : 0,
                        'customer_id' => isset($value['customer_id']) ? $value['customer_id'] : 0,
                        'customer_name' => isset($value['customers']['name']) ? $value['customers']['name'] : '',
                        'visit_type_id' => isset($value['visit_type_id']) ? $value['visit_type_id'] : 0,
                        'type_name' => isset($value['visittypename']['type_name']) ? $value['visittypename']['type_name'] : '',
                        'report_title' => isset($value['report_title']) ? $value['report_title'] : 0,
                        'description' => isset($value['description']) ? $value['description'] : '',

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

    public function submitVisitReports(Request $request)
    {
        try
        { 

            $user = $request->user();
            $validator = Validator::make($request->all(), [
                'checkin_id' => 'nullable|exists:check_in,id',
                'customer_id' => 'nullable|exists:customers,id',
                'visit_type_id' => 'nullable|exists:visit_types,id',
                'description' => 'required|string|max:1540',
            ]); 
            if ($validator->fails()) {
                return response()->json(['status' => 'error','message' =>  $validator->errors()], $this->badrequest); 
            }
            if($request->file('image')){
                $image = $request->file('image');
                $filename = 'checkin';
                $request['visit_image'] = fileupload($image, 'checkin', $filename);
            }

            if($report_id = VisitReport::insertGetId([
                'checkin_id' => isset($request['checkin_id']) ? $request['checkin_id'] : null, 
                'user_id' => $user->id, 
                'customer_id' => isset($request['customer_id']) ? $request['customer_id'] : null, 
                'visit_type_id' => isset($request['visit_type_id']) ? $request['visit_type_id'] : null, 
                //'report_title' => isset($request['report_title']) ? $request['report_title'] : '', 
                'description' => isset($request['description']) ? $request['description'] : '',
                'visit_image' => !empty($request['visit_image']) ? $request['visit_image'] : '',
                'created_by' => $user->id,
                'next_visit' => isset($request['next_visit']) ? date( 'Y-m-d H:i:s', strtotime( $request['next_visit'] )) : null,
                'created_at' => getcurentDateTime()
            ]))
            {
                return response()->json(['status' => 'success','message' => 'Report Submit successfully','report_id' => $report_id ], $this->successStatus); 
            }
            return response()->json(['status' => 'error','message' => 'Error in Report Submit' ], $this->badrequest);
        }
        catch(\Exception $e)
        {
            return response()->json(['status' => 'error','message' => $e->getMessage() ], $this->internalError);
        }        
    }
}
