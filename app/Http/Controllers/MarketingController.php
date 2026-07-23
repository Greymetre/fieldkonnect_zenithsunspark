<?php

namespace App\Http\Controllers;

use App\DataTables\DealerAppointmentDataTable;
use App\DataTables\MarketingDataTable;
use App\DataTables\MarketingDealerAppointmentDataTable;
use App\Exports\MarketingDealerAppointmentExport;
use App\Exports\MarketingMasterExport;
use App\Exports\MarketingMasterTemplate;
use App\Imports\MarketingMasterImport;
use App\Models\DealerAppointment;
use App\Models\Marketing;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Gate;
use Excel;

class MarketingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(MarketingDataTable $dataTable, Request $request)
    {
        $states = Marketing::latest()->get()->unique(function ($item) {
            return Str::lower($item->state);
        });
        $event_districts = Marketing::latest()->get()->unique(function ($item) {
            return Str::lower($item->event_district);
        });
        $event_under_names = Marketing::latest()->get()->unique(function ($item) {
            return Str::lower($item->event_under_name);
        });
        $cities = Marketing::latest()->get()->unique(function ($item) {
            return Str::lower($item->event_center);
        });
        $branchs = Marketing::latest()->get()->unique(function ($item) {
            return Str::lower($item->branch);
        });
        $categories = Marketing::latest()->get()
        ->unique(function ($item) {
            return Str::lower($item->category_of_participant);
        });
        $divisions = Marketing::latest()->get()
            ->unique(function ($item) {
                return Str::lower($item->division);
            });
            $users = User::whereIn('id', Marketing::latest()->pluck('created_by')->unique())
            ->latest()
            ->select('id', 'name')
            ->get();
        $branding_team_members = Marketing::latest()->get()->unique('branding_team_member');
        abort_if(Gate::denies('marketing_master_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return $dataTable->render('marketing.index', compact('states', 'event_districts', 'event_under_names', 'cities', 'branchs', 'categories', 'branding_team_members', 'divisions', 'users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('marketing.create');
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
     * @param  \App\Models\Marketing  $marketing
     * @return \Illuminate\Http\Response
     */
    public function show(Marketing $marketing)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Marketing  $marketing
     * @return \Illuminate\Http\Response
     */
    public function edit(Marketing $marketing)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Marketing  $marketing
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Marketing $marketing)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Marketing  $marketing
     * @return \Illuminate\Http\Response
     */
    public function destroy(Marketing $marketing)
    {
        //
    }

    /**
     * Exports and downloads the Marketing Master template as an Excel file.
     *
     * This function checks if the user has permission to access the marketing
     * master template. If denied, it aborts with a 403 Forbidden response.
     * Otherwise, it clears any existing output buffers, starts a new output
     * buffer, and returns the download of the 'Marketing Master.xlsx' file.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */

    public function marketings_template(Request $request)
    {
        abort_if(Gate::denies('marketing_master_template'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new MarketingMasterTemplate, 'Marketing Master Template.xlsx');
    }

    /**
     * Uploads and imports the Marketing Master data from a Google Drive link
     * or an Excel file.
     *
     * This function checks if the user has permission to upload the marketing
     * master data. If denied, it aborts with a 403 Forbidden response.
     * Otherwise, it clears any existing output buffers, starts a new output
     * buffer, and imports the data from the Google Drive link or the Excel
     * file into the Marketing model.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function marketings_upload(Request $request)
    {
        abort_if(Gate::denies('marketing_master_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        $googleDrivelink = $request->input('google_drivelink');
        $countOfParticipant = $request->input('count_of_participant');
        Excel::import(new MarketingMasterImport($googleDrivelink, $countOfParticipant), request()->file('import_file'));
        return redirect('marketings')->with('success', 'Data imported and updated successfully.');
    }

    /**
     * Downloads the Marketing Master data as an Excel file.
     *
     * This function checks if the user has permission to download the marketing
     * master data. If denied, it aborts with a 403 Forbidden response.
     * Otherwise, it clears any existing output buffers, starts a new output
     * buffer, and returns the download of the 'Marketing Master.xlsx' file.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download(Request $request)
    {
        abort_if(Gate::denies('marketing_master_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new MarketingMasterExport($request), 'Marketing Master.xlsx');
    }

    public function marketings_new_dealer(MarketingDealerAppointmentDataTable $dataTable, Request $request)
    {
        $divisions = DealerAppointment::groupBy('division')->pluck('division');
        return $dataTable->render('marketing.new_dealer', compact('divisions'));
    }

    public function dealer_board_installation(Request $request)
    {
        $dealerAppointment = DealerAppointment::find($request->dealer_id);
        $dealerAppointment->board_install_date = date('Y-m-d');
        $dealerAppointment->dealer_board = 1;
        $dealerAppointment->save();
        if ($request->hasFile('dealer_board')) {
            $file = $request->file('dealer_board');
            $customname = time() . '.' . $file->getClientOriginalExtension();
            $dealerAppointment->addMedia($file)
                ->usingFileName($customname)
                ->toMediaCollection('dealer_board', 's3');
        }
        return redirect()->route('marketing.new_dealer')->with('message_success', 'Board installation date and image updated successfully')->withInput();
    }

    public function dealer_welcome_kit(Request $request)
    {
        $dealerAppointment = DealerAppointment::find($request->dealer_id);
        $dealerAppointment->welcome_kit_date = date('Y-m-d');
        $dealerAppointment->welcome_kit = 1;
        $dealerAppointment->save();
        if ($request->hasFile('welcome_kit')) {
            $file = $request->file('welcome_kit');
            $customname = time() . '.' . $file->getClientOriginalExtension();
            $dealerAppointment->addMedia($file)
                ->usingFileName($customname)
                ->toMediaCollection('welcome_kit', 's3');
        }
        return redirect()->route('marketing.new_dealer')->with('message_success', 'Welcome kit date and invoice updated successfully')->withInput();
    }

    public function new_dealer_download(Request $request)
    {
        abort_if(Gate::denies('marketing_dealer_appointment_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        // return $request;
        return Excel::download(new MarketingDealerAppointmentExport($request), 'new_dealer_appointment.xlsx');
    }
}
