<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\OpeningStock;
use App\Models\Branch;

use App\Imports\OpeningStockImport;
use App\Exports\OpeningStockExport;

use Gate;
use DB;
use Excel;
use Validator;
use DataTables;

class OpeningStockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_if(Gate::denies('opening_stock_view'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view('opening-stocks.index');
    }

    public function getOpeningStocks(Request $request){
       abort_if(Gate::denies('opening_stock_view'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $query = OpeningStock::with([
            'warehouse',
        ])->latest()->newQuery();

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
            ->addIndexColumn()
           
            ->rawColumns(['action'])
            ->make(true);
    }

    // import 
    public function openingStockImport(Request $request)
    {
        try {
            abort_if(Gate::denies('opening_stock_import'), Response::HTTP_FORBIDDEN, '403 Forbidden');

            if (!$request->hasFile('import_file')) {
                return back()->with('error', 'No file uploaded.');
            }

            if (ob_get_contents()) ob_end_clean();           
            $file = $request->file('import_file'); // Get the uploaded file

            if ($file) {
                // Pass the file explicitly when creating an instance of OpeningStockImport
                $import = new OpeningStockImport($file);
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
        abort_if(Gate::denies('opening_stock_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new OpeningStockExport($request), 'opening-stocks.xlsx');
    }
}
