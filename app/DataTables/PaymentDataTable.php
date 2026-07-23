<?php

namespace App\DataTables;

use App\Models\Payment;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\Auth;

class PaymentDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables()
        ->eloquent($query)
        ->addIndexColumn()
        ->editColumn('created_at', function($data)
        {
            return isset($data->created_at) ? date('d/m/Y',strtotime($data->created_at)) : '';
        })
        ->editColumn('payment_date', function($data)
        {
            return isset($data->payment_date) ? $data->payment_date : '';
        })
        ->addColumn('action', function ($query) {
            $btn = '';
            // if(auth()->user()->can('payments_edit'))
            // {
                 $btn = $btn.'<a href="'.url("payments/".encrypt($query->id).'/edit') .'" class="btn btn-info btn-just-icon btn-sm" title="Edit Payment">
                                    <i class="material-icons">edit</i>
                                </a>';
            // }
            // if(auth()->user()->can('payments_show'))
            // {
                 $btn = $btn.'<a href="'.url("payments/".encrypt($query->id)).'" class="btn btn-warning btn-just-icon btn-sm" title="Payment View">
                                    <i class="material-icons">visibility</i>
                                </a>';
            // }
            // if(auth()->user()->can('payments_delete'))
            // {
                $btn = $btn.' <a href="" class="btn btn-danger btn-just-icon btn-sm delete" value="'.$query->id.'" title="Delete Payment">
                                <i class="material-icons">clear</i>
                              </a>';
            // }
             return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">'.$btn.'</div>';
        });

    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Payment $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Payment $model)
    {
        return $model->latest()->newQuery();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
                    ->setTableId('payment-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->orderBy(1)
                    ->parameters([
                        'dom'          => 'Bfrtip',
                        'buttons'      => ['excel', 'print'],
                    ]);
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
                  ->width(10), 
            Column::make('customer_name'),
            Column::make('payment_date'),
            Column::make('payment_mode'),
            Column::make('payment_type'),
            Column::make('amount'),
        ];
    }
}
