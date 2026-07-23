<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExpensesTypeRequest;
use App\Http\Requests\UpdateExpensesTypeRequest;
use App\Models\ExpensesType;
use Illuminate\Http\Request;
use DataTables;

class ExpensesTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {  
        if ($request->ajax()) {
            $data = ExpensesType::latest();

            return Datatables::of($data)
                ->addIndexColumn()

                ->addColumn('allowance_type', function ($query) {
                    $allowance_type = Config('constants.allowance_type');
                    foreach($allowance_type as $k=>$val){
                        if($k == $query->allowance_type_id){
                            return $val;
                        }
                    }
                })

                ->addColumn('is_active', function ($query) {
                      $active = ($query->is_active == '1') ? 'checked="" value="'.$query->is_active.'"' : 'value="'.$query->is_active.'"';
                      return '<div class="togglebutton">
                          <label>
                            <input type="checkbox"'.$active.' id="'.$query->id.'" class="activeRecord">
                            <span class="toggle"></span>
                          </label>
                        </div>';
              })
                
                ->addColumn('action', function ($query) {
                    $btn = '';
                    $activebtn = '';

                    $btn = $btn . '<a href="'.route("expenses_type.edit", ["expenses_type" => $query->id]).'" class="btn btn-info btn-just-icon btn-sm" title="' . trans('panel.global.edit') . ' ' . trans('panel.expenses_type.title_singular') . '">
                               <i class="material-icons">edit</i>
                                </a>';

                    return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                                            ' . $btn . '
                                        </div>' . $activebtn;
                })
                ->rawColumns(['allowance_type','is_active','action'])
                ->make(true);
        }
        return view('expenses_type.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {      
        $allowance_type = Config('constants.allowance_type');
        $pay_rolls = Config('constants.pay_roll');
        return view('expenses_type.create', compact('allowance_type','pay_rolls'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreExpensesTypeRequest  $request
     * @return \Illuminate\Http\Response
     */
     public function store(StoreExpensesTypeRequest $request)
     {          
        $expensesType = new ExpensesType();
        $expensesType->name = $request->name;
        $expensesType->rate = $request->rate!=null?$request->rate:0.00;
        $expensesType->allowance_type_id = $request->allowance_type_id;
        $expensesType->payroll_id = $request->payroll_id;
        $expensesType->save();
        return redirect(route('expenses_type.index'))->with('message_success', 'Data Store Successfully');
     }

    // /**
    //  * Display the specified resource.
    //  *
    //  * @param  \App\Models\ExpensesType  $expensesType
    //  * @return \Illuminate\Http\Response
    //  */
    // public function show(ExpensesType $expensesType)
    // {
    //     //
    // }

     /**
      * Show the form for editing the specified resource.
      *
      * @param  \App\Models\ExpensesType  $expensesType
      * @return \Illuminate\Http\Response
      */
     public function edit(ExpensesType $expensesType)
     {  
        $allowance_type = Config('constants.allowance_type');
        $pay_rolls = Config('constants.pay_roll');
        return view('expenses_type.edit', compact('expensesType', 'allowance_type','pay_rolls'));
     }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateExpensesTypeRequest  $request
     * @param  \App\Models\ExpensesType  $expensesType
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateExpensesTypeRequest $request, ExpensesType $expensesType)
    {
        $expensesType->name = $request->name;
        $expensesType->rate = $request->rate;
        $expensesType->allowance_type_id = $request->allowance_type_id;
        $expensesType->payroll_id = $request->payroll_id;
        $expensesType->save();
        return redirect(route('expenses_type.index'))->with('message_success', 'Data Updated Successfully');
    }

    // /**
    //  * Remove the specified resource from storage.
    //  *
    //  * @param  \App\Models\ExpensesType  $expensesType
    //  * @return \Illuminate\Http\Response
    //  */
    // public function destroy(ExpensesType $expensesType)
    // {
    //     //
    // }

    public function changeStatus(Request $request){
        $expenses_type = ExpensesType::find($request->id);
        if($request->active == '0'){
            $expenses_type->is_active = 1;
        }else{
            $expenses_type->is_active = 0;
        }

        $expenses_type->save();
        return response()->json(['status'=>'success', 'message'=>'Status changed successfully']);
    }
}
