<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClaimGeneration;
use App\Models\Customers;
use App\Models\Complaint;
use App\Models\ClaimGenerationDetail;
use App\Exports\ClaimExport;

use App\DataTables\ClaimGenerationDatatable;
use App\DataTables\ClaimGenerationSingleDatabale;

use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

use DataTables;
use Validator;
use Gate;
use Auth;
use Excel;
use Dompdf\Dompdf;
use Dompdf\Options;

class ClaimGenerationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct() 
    {     
        $this->middleware('auth');   
        $this->claim_generation = new ClaimGeneration();
        $this->path = 'claim_generations';
    }


    public function index()
    {
        abort_if(Gate::denies('claim_generation_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
         $service_centers = Customers::where('customertype', '4')->select('id', 'name', 'customer_code')->get();
        return view('claim-generation.index' , compact('service_centers'));
    }

    public function getClaims(ClaimGenerationDatatable $dataTable, Request $request)
    {
        return $dataTable->render('claim-generation.index');
    }

    public function getClaimsSingle(ClaimGenerationSingleDatabale $dataTable, Request $request)
    {
        return $dataTable->render('claim-generation.show');
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

    public function ClaimGenerationExport(Request $request){
        abort_if(Gate::denies('export_claim_report'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new ClaimExport($request), 'claim.xlsx');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort_if(Gate::denies('generate_claim'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $request->validate([
            'start_month' => 'required',
            'end_month'   => 'required',
        ]);
        try {
            $startDate = Carbon::createFromFormat('F Y', $request->start_month)->startOfMonth()->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('F Y', $request->start_month)->endOfMonth()->endOfDay()->format('Y-m-d H:i:s');

            if(!empty($request->service_center)){
                $complaints = Complaint::with(['service_bill.service_bill_products' , 'service_center_details'])->whereHas('service_bill', function ($query) use ($startDate, $endDate) {
                    $query->where('status', 3)
                          ->whereBetween('updated_at', [$startDate, $endDate]); // Ensures full range is covered
                })
                ->where('service_center' , $request->service_center)
                ->get();
            }else{
                 $complaints = Complaint::with(['service_bill.service_bill_products' , 'service_center_details'])->whereHas('service_bill', function ($query) use ($startDate, $endDate) {
                    $query->where('status', 3)
                          ->whereBetween('updated_at', [$startDate, $endDate]); // Ensures full range is covered
                })
                ->whereNotNull('service_center')
                ->get();
            }
                     
           $formatted_date = Carbon::createFromFormat('F Y', $request->start_month)->startOfMonth();

           $month = $formatted_date->format('M'); // Short month format (e.g., "Feb")
           $year = $formatted_date->format('Y');
           $claim_date = $formatted_date->format("Y-m-d");
           if ($complaints->isNotEmpty()) {
                // Group complaints by service center
                $groupedComplaints = $complaints->groupBy('service_center');
                $serviceCentersName = [];

                foreach ($groupedComplaints as $serviceCenter => $centerComplaints) {
                    $firstComplaint = $centerComplaints->first(); // Get first complaint for details

                    // Generate service center acronym
                    $serviceCenterAcronym = collect(explode(' ', $firstComplaint->service_center_details->name ?? 'Unknown Name'))
                        ->map(fn($word) => strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $word), 0, 1))) // Clean & get first letter
                        ->implode('');

                    // Create claim number
                    $claimNumber = "#{$serviceCenterAcronym}-{$month}-{$year}";

                    // Calculate total claim amount for this service center
                    $totalByServiceCenter = $centerComplaints->sum(fn($complaint) => 
                        $complaint->service_bill 
                            ? (float) $complaint->service_bill->service_bill_products->sum('subtotal') 
                            : 0.0
                    );

                    // Create or update ClaimGeneration
                    $claimGeneration = ClaimGeneration::updateOrCreate(
                        [
                            'month'             => $month,
                            'year'              => $year,
                            'service_center_id' => $serviceCenter,
                        ],
                        [
                            'claim_number' => $claimNumber,
                            'claim_amount' => $totalByServiceCenter,
                            'claim_date'   => $claim_date,
                        ]
                    );

                    // Insert ClaimGenerationDetails for each complaint under this service center
                    if ($claimGeneration) {
                        foreach ($centerComplaints as $complaint) {
                            ClaimGenerationDetail::updateOrCreate(
                                [
                                    'claim_generation_id' => $claimGeneration->id,
                                    'complaint_id'        => $complaint->id
                                ],
                                []
                            );
                        }
                    }

                    // Collect service center names for success message
                    $serviceCentersName[] = $firstComplaint->service_center_details->name ?? "Unknown Name";
                }

                // Success message with service centers included
                return redirect()->back()->with(
                    'message_success', 
                    'Your claim has been generated for ' . implode(', ', $serviceCentersName) . ' for ' . $month . ' - ' . $year
                );
            }else{
                return redirect()->back()->with(
                    'message_danger', 
                    'No claims found  in ' . $month .' - '. $year
                );    
            }
        } catch (\Exception $e) {
              return redirect()->back()->with(
                'message_danger', 
                'An error occurred: ' . $e->getMessage()
            );
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        abort_if(Gate::denies('claim_view'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $id = decrypt($id);
        $claimGeneration = ClaimGeneration::find($id);
        if(!$claimGeneration){
          return redirect()->back()->with('message_danger', 'Record not found');
        }
        return view('claim-generation.show', compact('claimGeneration'));
    }

    public function claimGenerationPdf($id){
        abort_if(Gate::denies('claim-pdf-generate'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $claimGeneration = ClaimGeneration::with(['claim_generation_details'])->find($id);
        if(!$claimGeneration){
          return redirect()->back()->with('message_danger', 'Record not found');
        }
         // return view('claim-generation.generate-claim-pdf', compact('claimGeneration'));
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $html = view('claim-generation.generate-claim-pdf', compact('claimGeneration'))->render();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        // Directly download the PDF
        return $dompdf->stream('MspActivity_' . time() . '.pdf', ['Attachment' => true]);

       
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        abort_if(Gate::denies('claim_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $id = decrypt($id);
        $claimGeneration = ClaimGeneration::find($id);
        if(!$claimGeneration){
          return redirect()->back()->with('message_danger', 'Record not found');
        }
        return view('claim-generation.create', compact('claimGeneration'));
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
        abort_if(Gate::denies('claim_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
         $request->validate([
            'asc_bill_no' => 'required',
            'asc_bill_date' => 'required',
            'asc_bill_amount' => 'required',
            'courier_details' => 'required',
            'courier_date' => 'required',
            'claim_sattlement_details' => 'required',
        ]);

        try{
          $id = decrypt($id);
          $claimGeneration = ClaimGeneration::find($id);
          if(!$claimGeneration){
             return redirect()->back()->with('message_danger', 'Record not found');
          }
          $data = $request->all();
          $data['courier_date'] = cretaDate($request->courier_date);
          $data['asc_bill_date'] = cretaDate($request->asc_bill_date);
          $claimGeneration->update($data);
           return Redirect::to('claim-generation')->with(
                'message_success', 
                'Claimed Updated Succussfully'
            );
        }catch (\Exception $e) {
              return redirect()->back()->with(
                'message_danger', 
                'An error occurred: ' . $e->getMessage()
            );
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
        //
    }
}
