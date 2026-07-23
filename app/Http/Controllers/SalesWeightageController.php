<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Designation;
use App\Models\Division;
use App\Models\salesWeightage;
use App\Models\Appraisal;
use Illuminate\Http\Request;
use DataTables;
use Validator;
use DB;

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
            //$data = salesWeightage::with(['devisions'])->latest();

           //$data = salesWeightage::with(['devisions'])->select('display_name')->groupBy('display_name');

          $data = salesWeightage::with('devisions')->select('display_name','financial_year','division_id')->groupBy('display_name','financial_year','division_id');

            return Datatables::of($data)
                ->addIndexColumn()
                // ->addColumn('weightage', function ($query) {
                //     return $query->weightage . '%';
                // })
                 ->addColumn('devision', function ($query) {
                    return $query->devisions->division_name??'';
                })
                ->addColumn('action', function ($query) {
                    $btn = '';
                    $activebtn = '';
                    // if(auth()->user()->can(['tour_edit']))
                    // {

                    $btn = $btn . '<a href="'.route("sales_weightage.edit", ["sales_weightage" => $query->display_name]).'" class="btn btn-info btn-just-icon btn-sm" title="' . trans('panel.global.edit') . ' ' . trans('panel.sales_weightage.title_singular') . '">
                               <i class="material-icons">edit</i>
                                </a>';
                    $btn = $btn . ' <a href="javascript:void(0)" class="btn btn-danger btn-just-icon btn-sm delete" value="' . $query->id . '" title="Delete Sales Weightage">
                                <i class="material-icons">clear</i>
                               </a>';

                    return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                                            ' . $btn . '
                                        </div>' . $activebtn;
                })
                ->rawColumns(['weightage', 'action','devision'])
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
            'name' => 'required|array',
            'weightage' => 'required|array',
            'division' => 'required',
            'financial_year' => 'required',
            'department' => 'required',
            'designation' => 'required|array',
            //'designation' => 'required',
            'category' => 'required|array',
            'indicator' => 'required|array',
            'annum_target' =>'required|array',
            'display_name' => 'required|unique:'.with(new salesWeightage)->getTable().',display_name',
        ]);
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        //$name = $request->name;
         $weightage = $request->weightage;



         $designation = $request->designation;
         if (!empty($designation)) {
         $designation = implode(",", $designation);
         } else {
         $designation = '';
         }


         /*


        $name = $request->name;
         if (!empty($name)) {
         $name = implode(",", $name);
         } else {
         $name = '';
         }

         $weightage = $request->weightage;
         if (!empty($weightage)) {
         $weightage = implode(",", $weightage);
         } else {
         $weightage = '';
         }

         $category = $request->category;
         if (!empty($category)) {
         $category = implode(",", $category);
         } else {
         $category = '';
         }

         $indicator = $request->indicator;
         if (!empty($indicator)) {
         $indicator = implode(",", $indicator);
         } else {
         $indicator = '';
         }

         $annum_target = $request->annum_target;
         if (!empty($annum_target)) {
         $annum_target = implode(",", $annum_target);
         } else {
         $annum_target = '';
         }


        salesWeightage::create([
            'name' => $name,
            'weightage' => $weightage,
            'division_id' => $request->division,
            'department_id' => $request->department,
            'designation_id' => $designation,
            'category_name' => $category,
            'indicator' => $indicator,
            'annum_target' => $annum_target,
            'financial_year' => $request->financial_year,
            'display_name' => $request->display_name,
        ]);


        */



        // salesWeightage::create([
        //     'name' => $name,
        //     'weightage' => $weightage,
        //     'division_id' => $request->division,
        //     'department_id' => $request->department,
        //     'designation_id' => $designation,
        //     'category_name' => $request->category,
        //     'indicator' => $request->indicator,
        //     'annum_target' => $request->annum_target,
        //     'display_name' => $request->display_name,
        // ]);

        


      if(!empty($request['name']))
        {
            foreach ($request['name'] as $key => $weightage_name) {
        $salesWeightage = salesWeightage::create(
          [ 
            'name' => $weightage_name??NULL,
            'weightage' => $weightage[$key],
            'division_id' => $request->division??NULL,
            'department_id' => $request->department??NULL,
            'designation_id' => $designation??NULL,
            'category_name' => $request->category[$key],
            'indicator' => $request->indicator[$key],
            'annum_target' => $request->annum_target[$key],
            'display_name' => $request->display_name??NULL,
            'financial_year' => $request->financial_year??NULL,
          ]
          );
         }
       }

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
        // $this->sales_weightage = salesWeightage::find($id);
        $this->sales_weightage = salesWeightage::select([
         DB::raw('GROUP_CONCAT(id) as ids'),
         DB::raw('GROUP_CONCAT(name) as names'),
         DB::raw('GROUP_CONCAT(weightage) as weightages'),
         DB::raw('division_id'),  
         DB::raw('department_id'),
         DB::raw('designation_id'),
         DB::raw('GROUP_CONCAT(category_name) as category_names'),
         DB::raw('GROUP_CONCAT(indicator) as indicators'),
         DB::raw('GROUP_CONCAT(annum_target) as annum_targets'),
         DB::raw('display_name'), 
         DB::raw('financial_year'), 
        ])->where('display_name',$id)->groupBy('display_name','division_id','department_id','designation_id','financial_year')->first();

       // dd($this->sales_weightage);

        $devisions = Division::all();
        $departments = Department::all();
        $designations = Designation::all();

        return view('sales_weightage.edit', compact('devisions', 'departments', 'designations'))->with('sales_weightage', $this->sales_weightage);
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
          
        $validator = Validator::make($request->all(), [
            'name' => 'required|array',
            'weightage' => 'required|array',
            'division' => 'required',
            'department' => 'required',
            'designation' => 'required|array',
            //'designation' => 'required',
            'financial_year' => 'required',
            'category' => 'required|array',
            'indicator' => 'required|array',
            'annum_target' =>'required|array',
            'display_name' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }


        $sales_weightage = salesWeightage::find($id);
        if($sales_weightage){

            //  $designation = $request->designation;
            //  if (!empty($designation)) {
            //  $designation = implode(",", $designation);
            //  } else {
            //  $designation = '';
            //  }

            // $sales_weightage->name = $request->name;
            // $sales_weightage->weightage = $request->weightage;
            // $sales_weightage->division_id = $request->division;
            // $sales_weightage->department_id = $request->department;
            // $sales_weightage->designation_id = $designation;
            // $sales_weightage->category_name = $request->category;
            // $sales_weightage->indicator = $request->indicator;
            // $sales_weightage->annum_target = $request->annum_target;
            // $sales_weightage->display_name = $request->display_name;
            // $sales_weightage->save();


         $designation = $request->designation;
         if (!empty($designation)) {
         $designation = implode(",", $designation);
         } else {
         $designation = '';
         }


        $name = $request->name;
         if (!empty($name)) {
         $name = implode(",", $name);
         } else {
         $name = '';
         }

         $weightage = $request->weightage;
         if (!empty($weightage)) {
         $weightage = implode(",", $weightage);
         } else {
         $weightage = '';
         }

         $category = $request->category;
         if (!empty($category)) {
         $category = implode(",", $category);
         } else {
         $category = '';
         }

         $indicator = $request->indicator;
         if (!empty($indicator)) {
         $indicator = implode(",", $indicator);
         } else {
         $indicator = '';
         }

         $annum_target = $request->annum_target;
         if (!empty($annum_target)) {
         $annum_target = implode(",", $annum_target);
         } else {
         $annum_target = '';
         }

        $sales_weightage->name = $name;
        $sales_weightage->weightage = $weightage;
        $sales_weightage->division_id = $request->division;
        $sales_weightage->department_id = $request->department;
        $sales_weightage->designation_id = $designation;
        $sales_weightage->category_name = $category;
        $sales_weightage->indicator = $indicator;
        $sales_weightage->annum_target = $annum_target;
        $sales_weightage->display_name = $request->display_name;
        $sales_weightage->financial_year = $request->financial_year;
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

    public function multiupdate(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|array',
            'weightage' => 'required|array',
            'division' => 'required',
            'department' => 'required',
            'designation' => 'required|array',
            //'designation' => 'required',
            'financial_year' => 'required',
            'category' => 'required|array',
            'indicator' => 'required|array',
            'annum_target' =>'required|array',
            'display_name' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

         $weightage = $request->weightage;
         $designation = $request->designation;
         if (!empty($designation)) {
         $designation = implode(",", $designation);
         } else {
         $designation = '';
         } 


        if(!empty($request['weightages_ids']))
        {  
            $data_array = array();
            foreach ($request['weightages_ids'] as $key => $weightages_id) {
        $salesWeightage = salesWeightage::updateOrCreate(['id' => $weightages_id],
          [ 
            'name' => $request->name[$key]??NULL,
            'weightage' => $weightage[$key],
            'division_id' => $request->division??NULL,
            'department_id' => $request->department??NULL,
            'designation_id' => $designation??NULL,
            'category_name' => $request->category[$key],
            'indicator' => $request->indicator[$key],
            'annum_target' => $request->annum_target[$key],
            'display_name' => $request->display_name??NULL,
            'financial_year' => $request->financial_year??NULL,
          ]
          );
         $data_array[] = $salesWeightage->id;
         }

      $kra_id = salesWeightage::where(['display_name'=> $request->display_name])->whereNotIn('id',$data_array)->pluck('id');
      Appraisal::whereIn('weightage_id',$kra_id)->delete();
      salesWeightage::where(['display_name'=> $request->display_name])->whereNotIn('id',$data_array)->delete();

      return redirect(route('sales_weightage.index'))->with('message', 'Sales weightage updated successfully');  

       }else{

      return redirect(route('sales_weightage.index'))->with('error', 'Somthing went wroung');

       }


    }
}
