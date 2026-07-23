<?php

namespace App\Http\Controllers;

use App\Models\Field;
use Illuminate\Http\Request;
use App\Models\FieldData;
use App\Models\SurveyData;
use App\Models\CustomerType;
use App\Models\Division;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use DataTables;
use Validator;
use Gate;
use App\DataTables\FieldsDataTable;

class FieldController extends Controller
{
    public function __construct() 
    {     
        $this->middleware('auth');   
        $this->fields = new Field();
        
    }
    public function index(FieldsDataTable $dataTable)
    {
        //abort_if(Gate::denies('fields_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return $dataTable->render('fields.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $customertype = CustomerType::select('id','customertype_name')->orderBy('id','desc')->get();
        $divisions = Division::select('id','division_name')->orderBy('id','desc')->get();
        return view('fields.create',compact('customertype','divisions'))->with('fields',$this->fields);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         try
        { 
            //$useraccess = !empty($request['id']) ? 'fields_edit' : 'fields_create' ;
            //abort_if(Gate::denies($useraccess), Response::HTTP_FORBIDDEN, '403 Forbidden');
            $request['created_by'] = Auth::user()->id;
            $request['is_required'] = ($request['is_required'] == 'on') ? true : false;
            $request['is_multiple'] = ($request['is_multiple'] == 'on') ? true : false;
            $request['active'] = 'Y';
            if($fields = Field::create($request->except(['_token'])))
            {
                if($request['details'])
                {
                    foreach ($request['details'] as $key => $value) {
                       if(!empty($value['value']))
                        {
                          FieldData::create([
                            'field_id' => $fields['id'],
                            'value' => $value['value'],
                           ]);
                        }
                    }
                }
              return Redirect::to('fields')->with('message_success', 'Field Store Successfully');
            }
            return redirect()->back()->with('message_danger', 'Error in Data Store')->withInput();  
        }     
        catch(\Exception $e)
        {
          return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //abort_if(Gate::denies('fields_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $id = decrypt($id);
        $fields = Field::find($id);
        $customertype = CustomerType::select('id','customertype_name')->orderBy('id','desc')->get();
        $divisions = Division::select('id','division_name')->orderBy('id','desc')->get();
        return view('fields.create',compact('customertype','divisions'))->with('fields',$fields);
    }

    public function update(Request $request, $id)
    {
         try
        { 
            if(Field::where('id',$id)->update($request->except(['_token','_method','image','details'])))
            {
              if($request['details'])
                {
                  $fieldValue = array();
                    foreach ($request['details'] as $key => $value) {
                      if(!empty($value['value']))
                      {
                        FieldData::updateOrCreate(
                          ['field_id' => $id, 'value' => $value['value']],
                          ['field_id' => $id , 'value' => $value['value'] 
                        ]);
                        array_push($fieldValue, $value['value']);
                      }
                    }
                  FieldData::where('field_id', $id)->whereNotIn('value',$fieldValue)->delete();
                }

                return Redirect::to('fields')->with('message_success', 'Data Update Successfully');
            }
            return redirect()->back()->with('message_danger', 'Error in Data Store')->withInput(); 
        }     
        catch(\Exception $e)
        {
          return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        // abort_if(Gate::denies('fields_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        SurveyData::where('field_id',$id)->delete();
        $field = Field::find($id);
        // if(EnquireFields::where('field_id',$field['field_name'])->count() >= 1)
        // {
        //     return response()->json(['status' => 'error','message' => 'This field already in use!']);
        // }
        if($field->delete())
        {
            return response()->json(['status' => 'success','message' => 'Field deleted successfully!']);
        }
        return response()->json(['status' => 'error','message' => 'Error in User Delete!']);
    }
    
    public function active(Request $request)
    {
        if(Field::where('id',$request['id'])->update(['active' => ($request['active'] == 'Y') ? 'N' :'Y']))
        {
            $message = ($request['active'] == 'Y') ? 'Inactive' :'Active';
            return response()->json(['status' => 'success','message' => 'Field '.$message.' Successfully!']);
        }
        return response()->json(['status' => 'error','message' => 'Error in Status Update']);
    }
}
