<?php

namespace App\Http\Controllers;

use App\Exports\OpeningQuantityExport;
use App\Imports\OpeningQuantityImport;
use App\Models\Branch;
use App\Models\BranchOprningQuantity;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Gate;
use DB;
use Excel;
use Validator;
use DataTables;

class BranchOprningQuantityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_if(Gate::denies('branch_opening_qty_view'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view('opening-quantity.index');
    }

    public function getOpeningquantity(Request $request){
       abort_if(Gate::denies('branch_opening_qty_view'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $query = BranchOprningQuantity::latest()->newQuery();

         return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('branch_names', function ($data) {
                $branch_ids = explode(',', $data->branch_id);
                $branch_names = collect($branch_ids)
                    ->map(function ($branch_id) {
                        return Branch::find($branch_id)?->branch_name; // Use optional chaining to prevent errors
                    })
                    ->filter() // Remove any null values (if branch_id is invalid)
                    ->implode(', ');
                return $branch_names;
            })
            ->addColumn('qty_month', function ($data) {
                return $data->qty_month ? date('M-y', strtotime($data->qty_month)) : '-';
            })
            ->addIndexColumn()
           
            ->rawColumns(['qty_month'])
            ->make(true);
    }

    // import 
    public function openingStockImport(Request $request)
    {
        try {
            abort_if(Gate::denies('branch_opening_qty_import'), Response::HTTP_FORBIDDEN, '403 Forbidden');

            if (!$request->hasFile('import_file')) {
                return back()->with('error', 'No file uploaded.');
            }

            if (ob_get_contents()) ob_end_clean();           
            $file = $request->file('import_file'); // Get the uploaded file

            if ($file) {
                // Pass the file explicitly when creating an instance of OpeningStockImport
                $import = new OpeningQuantityImport($file);
                Excel::import($import, $file);
            } else {
                 return back()->with('message_error', 'Import successful!');
            }
    
            return back()->with('message_success', 'Import successful!');
        } catch (\Exception $e) {
             return back()->with('message_error', 'Import failed! ' . $e->getMessage());
        }
    }

     public function openingStockExport(Request $request){
        abort_if(Gate::denies('branch_opening_qty_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new OpeningQuantityExport($request), 'opening-quantity.xlsx');
    }
}
