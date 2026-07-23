<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Designation;
use App\Models\Division;
use App\Models\salesWeightage;
use Illuminate\Http\Request;
use DataTables;
use Validator;

class SalesWeightageController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->sales_weightage = new salesWeightage();
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // $data = Appraisal::whereIn('user_id', $all_reporting_user_ids)->latest();
            $data = salesWeightage::latest();

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('weightage', function ($query) {
                    return $query->weightage . '%';
                })
                ->addColumn('action', function ($query) {
                    $btn = '';
                    $activebtn = '';
                    // if(auth()->user()->can(['tour_edit']))
                    // {

                    $btn = $btn . '<a href="'.route("sales_weightage.edit", ["sales_weightage" => $query->id]).'" class="btn btn-info btn-just-icon btn-sm" title="' . trans('panel.global.edit') . ' ' . trans('panel.sales_weightage.title_singular') . '">
                               <i class="material-icons">edit</i>
                                </a>';
                    $btn = $btn . ' <a href="javascript:void(0)" class="btn btn-danger btn-just-icon btn-sm delete" value="' . $query->id . '" title="Delete Sales Weightage">
                                <i class="material-icons">clear</i>
                               </a>';

                    return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                                            ' . $btn . '
                                        </div>' . $activebtn;
                })
                ->rawColumns(['weightage', 'action'])
                ->make(true);
        }
        return view('sales_weightage.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $devisions = Division::all();
        $departments = Department::all();
        $designations = Designation::all();
        return view('sales_weightage.create', compact('devisions', 'departments', 'designations'))->with('sales_weightage', $this->sales_weightage);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'weightage' => 'required',
            'division' => 'required',
            'department' => 'required',
            'designation' => 'required|array',
            //'designation' => 'required',
            'category' => 'required',
            'indicator' => 'required',
            'annum_target' =>'required',
            'display_name' => 'required|unique:'.with(new salesWeightage)->getTable().',display_name',
        ]);
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $name = $request->name;
        $weightage = $request->weightage;

        $designation = $request->designation;
         if (!empty($designation)) {
         $designation = implode(",", $designation);
         } else {
         $designation = '';
         }


        salesWeightage::create([
           'name' => $name,
            'weightage' => $weightage,
            'division_id' => $request->division,
            'department_id' => $request->department,
            'designation_id' => $designation,
            'category_name' => $request->category,
            'indicator' => $request->indicator,
            'annum_target' => $request->annum_target,
            'display_name' => $request->display_name,
        ]);

        return redirect(route('sales_weightage.index'))->with('message', 'Sales weightage added successfully');
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
        $this->sales_weightage = salesWeightage::find($id);
         $devisions = Division::all();
        $departments = Department::all();
        $designations = Designation::all();

        return view('sales_weightage.create',compact('devisions', 'departments', 'designations'))->with('sales_weightage', $this->sales_weightage);;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $sales_weightage = salesWeightage::find($id);
        if($sales_weightage){
             $designation = $request->designation;
             if (!empty($designation)) {
             $designation = implode(",", $designation);
             } else {
             $designation = '';
             }

            $sales_weightage->name = $request->name;
            $sales_weightage->weightage = $request->weightage;
            $sales_weightage->division_id = $request->division;
            $sales_weightage->department_id = $request->department;
            $sales_weightage->designation_id = $designation;
            $sales_weightage->category_name = $request->category;
            $sales_weightage->indicator = $request->indicator;
            $sales_weightage->annum_target = $request->annum_target;
            $sales_weightage->display_name = $request->display_name;
            $sales_weightage->save();
            return redirect(route('sales_weightage.index'))->with('message', 'Sales weightage updated successfully');
        }else{
            return redirect(route('sales_weightage.index'))->with('error', 'Somthing went wroung');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $sales_weightage = salesWeightage::find($id);

        if($sales_weightage){
            $sales_weightage->delete();
            return response()->json(['status'=> true]);
        }else{
            return response()->json(['status'=> false]);
        }

    }
}
