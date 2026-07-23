<?php

namespace App\Http\Controllers;

use App\DataTables\SAPStockDataTable;
use App\Models\Category;
use App\Models\SapStock;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\PlannedSOP;
use App\Models\PlannedSopSaleData;
use App\Models\PrimarySales;
use App\Models\ProductDetails;
use Gate;

use App\Models\Customers;
use App\Models\DealerAppointment;
use App\Models\DealerAppointmentKyc;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use Laravel\Passport\Token;
use Auth;

class SapStockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(SAPStockDataTable $dataTable, Request $request)
    {
        // $users = User::whereNotNull('customerid')->get();

        // foreach ($users as $user) {
        //     $user_customer = Customers::find($user->customerid);
        //     if($user_customer){
        //         $user->active = $user_customer->active;
        //         $user->save();
        //     }
        // }

        // dd("Done");

        // $path = '/var/www/html/Dealer Appointment Tag EMP Details.xlsx';

        // if (!file_exists($path)) {
        //     return 'File not found!';
        // }

        // Excel::import(new class implements ToCollection {
        //     public function collection(Collection $rows)
        //     {
        //         foreach ($rows as $index => $row) {
        //             if ($index > 0) {
        //                 $dealerCode = $row[87];
        //                 $created_by = $row[88]; 
        //                 // dd($dealerCode, $created_by);
        //                 $kycD = DealerAppointmentKyc::where('dealer_code', $dealerCode)->first();
        //                 $appointment = DealerAppointment::where('id', $kycD->appointment_id)->first();
        //                 if($appointment){
        //                     $appointment->created_by = $created_by;
        //                     $appointment->save();
        //                 }
        //             }
        //         }
        //     }
        // }, $path);

        // dd('Import completed!');

        abort_if(Gate::denies('sap_stock_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return $dataTable->render('sap_stock.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SapStock  $sapStock
     * @return \Illuminate\Http\Response
     */
    public function show(SapStock $sapStock)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SapStock  $sapStock
     * @return \Illuminate\Http\Response
     */
    public function edit(SapStock $sapStock)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SapStock  $sapStock
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SapStock $sapStock)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SapStock  $sapStock
     * @return \Illuminate\Http\Response
     */
    public function destroy(SapStock $sapStock)
    {
        //
    }
}
