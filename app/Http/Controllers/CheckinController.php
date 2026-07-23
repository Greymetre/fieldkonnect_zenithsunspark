<?php

namespace App\Http\Controllers;

use App\Models\CheckIn;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use DataTables;
use Validator;
use Gate;
use App\DataTables\CheckinDataTable;
use Excel;
use App\Exports\CheckinExport;

class CheckinController extends Controller
{
    public function __construct() 
    {     
        $this->middleware('auth');   
        $this->checkin = new CheckIn();
        
    }

    public function index(CheckinDataTable $dataTable)
    {
        abort_if(Gate::denies('checkin_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return $dataTable->render('checkin.index');
    }
    public function download(Request $request)
    {
        ////abort_if(Gate::denies('visitreport_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new CheckinExport($request), 'checkin.xlsx');
    }
}
