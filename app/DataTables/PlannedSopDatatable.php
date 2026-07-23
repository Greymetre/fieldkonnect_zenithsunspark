<?php

namespace App\DataTables;

use App\Models\PlannedSOP;
use Illuminate\Http\Request;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PlannedSopDatatable extends DataTable
{
    
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->editColumn('created_at', function($data)
            {
                return isset($data->created_at) ? showdatetimeformat($data->created_at) : '';
            })
            ->editColumn('status' , function($data){
                 // if($data->status == 1){
                 //    return '<span class="badge badge-success">OPEN</span>';
                 // }else if(isset($data->status) && $data->status == "0"){
                 //    return '<span class="badge badge-danger">CANCEL</span>';
                 // }else{
                 //     return '<span class="badge badge-dager">CANCEL</span>';
                 // }
                if(isset($data->status)){
                    switch ($data->status) {
                        case 1:
                            return '<span class="badge badge-success">OPEN</span>';
                        case 0:
                            return '<span class="badge badge-danger">CANCEL</span>';
                        case 2:
                            return '<span class="badge badge-success">Verify</span>';
                        case 3:
                            return '<span class="badge badge-success">Approved</span>';
                        default:
                            return '<span class="badge badge-danger">CANCEL</span>'; // Fix typo: badge-dager → badge-danger
                    } 
                }

            })
            ->editColumn('planning_month' , function($data){
                 try{
                    if(isset($data->planning_month)){
                        return \Carbon\Carbon::parse($data->planning_month)->format('F Y');
                    }else{
                         return "Not Found";
                    }
                 }catch(\Exception $e){
                    return "Not Found";
                 }
            })
            ->addColumn('checkbox', function ($data) {
                if($data->getProduct->categories->id ==1 && (Auth::user()->hasRole('superadmin') || Auth::user()->hasRole('Sub_Admin') || Auth::user()->hasRole('PUMPCH'))){
                    return '<input type="checkbox" class="row-checkbox" value="' . $data->id . '">';
                }
            })
             ->addColumn('action', function ($query) {
                  $btn = '';
                  $activebtn ='';
                  if((auth()->user()->can(['sop_edit']) && $query->status == 1) || (auth()->user()->can(['sop_edit']) && Auth::user()->designation_id == "6" && $query->status == 2) || Auth::user()->hasRole('superadmin'))
                  {
                    $btn = $btn.'<a href="'.route('planned-sop.edit', encrypt($query->id)).'" class="btn btn-info btn-just-icon btn-sm edit mr-2" id="'.encrypt($query->id).'" title="'.trans('panel.global.edit').' SOP">
                          <i class="material-icons">edit</i>
                        </a>';
                  }
                  if (auth()->user()->can(['sop_cancel'])) {
                        $btn .= '<form action="' . route('planned-sop.update', encrypt($query->id)) . '" method="POST" class="update-form-' .$query->id . '" style="display:inline;">
                                    ' . csrf_field() . '
                                    ' . method_field('PUT') . '
                                    <input type="hidden" name="status" value="0">
                                    <button type="button" class="btn btn-warning btn-just-icon btn-sm update-sop mr-2" data-id="' . $query->id . '" title="Cancel SOP">
                                        <i class="material-icons">close</i>
                                    </button>
                                </form>';
                    }

                    if (auth()->user()->can(['sop_delete'])) {
                        $btn .= '<form action="' . route('planned-sop.destroy', encrypt($query->id)) . '" method="POST" class="mr-2 delete-form-' .$query->id . '" style="display:inline;">
                                ' . csrf_field() . '
                                ' . method_field('DELETE') . '

                                <button type="button" class="btn btn-danger btn-just-icon btn-sm delete-sop " data-id="' . $query->id . '" title="Delete SOP">
                                    <i class="material-icons">delete</i>
                                </button>
                            </form>';
                    }

                    if (auth()->user()->can(['verify_sop']) && $query->getProduct->categories->id ==1 && $query->status == 1) {
                        $btn .= '<form action="' . route('planned-sop.update', encrypt($query->id)) . '" method="POST" class="verify-form-' .$query->id . '"  style="display:inline;">
                                    ' . csrf_field() . '
                                    ' . method_field('PUT') . '
                                    <input type="hidden" name="status" value="2">
                                    <button type="button" class="btn btn-warning btn-just-icon btn-sm verify-sop mr-2" data-id="' . $query->id . '" title="Verify SOP">
                                        <i class="material-icons">check_circle</i>
                                    </button>
                                </form>';
                    }

                     if (auth()->user()->can(['approved_sop'])  && $query->getProduct->categories->id == 1 && $query->status == 2) {
                        $btn .= '<form action="' . route('planned-sop.update', encrypt($query->id)) . '" method="POST" class="approve-form-' .$query->id . '" style="display:inline;">
                                    ' . csrf_field() . '
                                    ' . method_field('PUT') . '
                                    <input type="hidden" name="status" value="3">
                                    <button type="button" class="btn btn-success btn-just-icon btn-sm approve-sop mr-2" data-id="' . $query->id . '" title="Approve SOP">
                                       <i class="material-icons">done</i>
                                    </button>
                                </form>';
                    }
                  return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                                '.$btn.'
                            </div>';
            })            
            ->rawColumns(['action' , 'status' , 'action' , 'checkbox']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Product $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(PlannedSOP $model, Request $request)
    {
        $filters = $request->all();
        $data = $model->with(['getProduct.subcategories' , 'getProduct.categories', 'getBranch']);
        
        if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Sub_Admin')){
            $data->whereRaw("FIND_IN_SET(?, view_only)", [Auth::user()->division_id]);
        }
        if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Sub_Admin')){
            $branch_ids = explode(',', Auth::user()->branch_id);
            $data->whereIn('branch_id' , $branch_ids);
        }
        foreach ($filters as $key => $value) {
             if (isset($value))  {
                switch ($key) {
                    case "created_by" : 
                    case 'top_sku':
                        $data->where($key, 'like', "%$value%");
                        break;
                    case "product_name" :
                    case "description": 
                    case "product_code":
                        $data->whereHas('getProduct', function ($q) use ($value , $key) {
                            $q->where($key, 'like', "%$value%");
                        });
                        break;
                    case "branch_name":
                        $data->whereHas('getBranch', function ($q) use ($value , $key) {
                            $q->where($key, 'like', "%$value%");
                        });
                        break;
                    case "category_name" : 
                        $data->whereHas('getProduct.categories', function ($q) use ($value) {
                            $q->where('category_name', 'like', "%$value%");
                        });
                        break;
                    case "division_id" : 
                        $data->whereHas('getProduct.categories', function ($q) use ($value) {
                            $q->where('id', $value);
                        });
                        break;
                    case "group_name" : 
                        $data->whereHas('getProduct.subcategories', function ($q) use ($value) {
                            $q->where('subcategory_name', 'like', "%$value%");
                        });
                        break;
                    case 'plan_next_month':
                    case 'budget_for_month':
                    case 'last_month_sale':
                    case 'last_three_month_avg':
                    case 'last_year_month_sale':
                    case 'sku_unit_price':
                    case 's_op_val':
                    case  'status' :
                    case 'order_id': 
                     $data->where($key, 'like', "$value");
                     break;
                }
            }
        }

        if(isset($request->planning_month)){
          try{
            $formatted_date = Carbon::createFromFormat('F Y', $request->planning_month)->startOfMonth();
            $planning_month = $formatted_date->format("Y-m-d");
            $data->whereDate('planning_month' , $planning_month);
          }catch(\Exception $e){
             $data = $data->latest()->newQuery();
          }
        }
        $data = $data->latest()->newQuery();
        return $data;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
                    ->setTableId('product-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->dom('Bfrtip')
                    ->orderBy(1)
                    ->buttons(
                        Button::make('create'),
                        Button::make('export'),
                        Button::make('print'),
                        Button::make('reset'),
                        Button::make('reload')
                    );
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            Column::computed('action')
                  ->exportable(false)
                  ->printable(false)
                  ->width(60)
                  ->addClass('text-center'),
            Column::make('id'),
            Column::make('add your columns'),
            Column::make('created_at'),
            Column::make('updated_at'),
        ];
    }
}
