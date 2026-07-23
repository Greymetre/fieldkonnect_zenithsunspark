<?php

namespace App\Http\Controllers\Api;

use App\DataTables\DealerAppointmentDataTable;
use App\Exports\DealerAppointmentExport;
use App\Models\Branch;
use App\Models\DealerAppointment;
use App\Models\DealerAppointmentKyc;
use App\Models\District;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Gate;
use Excel;
use Validator;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class DealerAppointmentController extends Controller
{
    public function getappointments(Request $request)
    {
        $user = $request->user();
        $braches = Branch::where('active', 'Y')->select('id', 'branch_name')->get();
        $user_ids = getUsersReportingToAuth($user->id);
        $users = User::whereDoesntHave('roles', function ($query) {
            $query->where('id', 29);
        })->where('active', 'Y')->whereIn('id', $user_ids)->select('id', 'name')->orderBy('name', 'asc')->get();
        $pageSize = $request->input('pageSize');
        $all_status = [['id' => '0', 'name' => 'Pending'], ['id' => '1', 'name' => 'Approved By Sales Team'], ['id' => '2', 'name' => 'Approved By Account'], ['id' => '3', 'name' => 'Approved By HO'], ['id' => '4', 'name' => 'Rejected']];
        $db_data = DealerAppointment::with('media','branch_details', 'district_details', 'city_details', 'appointment_kyc_detail', 'createdbyname')->whereIn('created_by', $user_ids);

        if ($request->startdate && $request->startdate != '' && $request->startdate != NULL && $request->enddate && $request->enddate != '' && $request->enddate != NULL) {

            $startDate = date('Y-m-d', strtotime($request->startdate));
            $endDate = date('Y-m-d', strtotime($request->enddate));
            $db_data = $db_data->whereDate('appointment_date', '>=', $startDate)
                ->whereDate('appointment_date', '<=', $endDate);
        }

        if ($request->status_id != '' && $request->status_id != NULL) {
            $db_data->where('approval_status', $request->status_id);
        }
        if ($request->created_by && $request->created_by != '' && $request->created_by != NULL) {
            $db_data->where('created_by', $request->created_by);
        }
        if ($request->branch && count($request->branch) > 0 && $request->branch != '' && $request->branch != NULL) {
            $db_data->whereIn('branch', $request->branch);
        }

        $db_data = (!empty($pageSize)) ? $db_data->latest()->paginate($pageSize) : $db_data->latest()->get();
        $data = collect([]);
        if ($db_data->isNotEmpty()) {
            foreach ($db_data as $key => $value) {
                if ($value->approval_status == '0') {
                    $db_data[$key]['approval_status'] =  'Pending';
                } elseif ($value->approval_status == '1') {
                    $db_data[$key]['approval_status'] =  'Approved By Sales Team';
                } elseif ($value->approval_status == '2') {
                    $db_data[$key]['approval_status'] =  'Approved By Account';
                } elseif ($value->approval_status == '3') {
                    $db_data[$key]['approval_status'] =  'Approved By HO';
                } elseif ($value->approval_status == '4') {
                    $db_data[$key]['approval_status'] =  'Rejected';
                }
                $data->push([
                    'id' => isset($value['id']) ? $value['id'] : '',
                    'branch' => isset($value['branch_details']) ? $value['branch_details']['branch_name'] : '',
                    'district' => isset($value['district_details']) ? $value['district_details']['district_name'] : '',
                    'approval_status' => isset($value['approval_status']) ? $value['approval_status'] : 'Pending',
                    'created_by' => ($value['createdbyname'] && isset($value['createdbyname']['name'])) ? $value['createdbyname']['name'] : '',
                    'created_by_id' => ($value['created_by'] && isset($value['created_by'])) ? $value['created_by'] : '',
                    'appointment_date' => isset($value['appointment_date']) ? date('d M Y', strtotime($value['appointment_date'])) : '',
                    'firm_name' => isset($value['firm_name']) ? $value['firm_name'] : '',
                    'place' => isset($value['place']) ? $value['place'] : '',
                    'division' => isset($value['division']) ? $value['division'] : '',
                    'certificate' => $value->getMedia('certificate')->count() > 0 && Storage::disk('s3')->exists($value->getMedia('certificate')[0]->getPath()) ? Storage::disk('s3')->url($value->getMedia('certificate')[0]->getPath()) : ''
                ]);
            }
        }

        return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $data, 'braches' => $braches, 'users' => $users, 'all_status' => $all_status], 200);
    }
    public function getappointmentsDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'appointment_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' =>  $validator->errors()], 400);
        }
        $user_ids = getUsersReportingToAuth();
        $db_data = DealerAppointment::with('branch_details', 'district_details', 'city_details', 'appointment_kyc_detail', 'createdbyname')->where('id', $request->appointment_id)->whereIn('created_by', $user_ids);

        $db_data = $db_data->latest()->get();
        if ($db_data->isNotEmpty()) {
            foreach ($db_data as $key => $value) {
                $data = [
                    'id' => isset($value['id']) ? $value['id'] : '',
                    'branch' => isset($value['branch_details']) ? $value['branch_details']['branch_name'] : '',
                    'created_by' => ($value['createdbyname'] && isset($value['createdbyname']['name'])) ? $value['createdbyname']['name'] : '',
                    'division' => isset($value['division']) ? $value['division'] : '',
                    'customertype' => isset($value['customertype']) ? $value['customertype'] : '',
                    'firm_name' => isset($value['firm_name']) ? $value['firm_name'] : '',
                    'contact_person' => isset($value['contact_person_name']) ? $value['contact_person_name'] : '',
                    'mobile_email' => isset($value['mobile_email']) ? $value['mobile_email'] : '',
                    'district' => isset($value['district_details']) ? $value['district_details']['district_name'] : '',
                    'city' => isset($value['city_details']) ? $value['city_details']['city_name'] : '',
                    'place' => isset($value['place']) ? $value['place'] : '',
                    'appointment_date' => isset($value['appointment_date']) ? date('d M Y', strtotime($value['appointment_date'])) : '',
                    'security_deposit' => isset($value['security_deposit']) ? $value['security_deposit'] : '',
                    'gst_details' => isset($value['gst_no']) ? $value['gst_no'] . '(' . $value['gst_type'] . ')' : '',
                    'firm_type' => isset($value['firm_type']) ? $value['firm_type'] : '',
                    'payment_term' => isset($value['payment_term']) ? $value['payment_term'] : '',
                    'credit_period' => isset($value['credit_period']) ? $value['credit_period'] : '',
                    'present_annual_turnover' => isset($value['present_annual_turnover']) ? $value['present_annual_turnover'] : '',
                    'credit_limit' => isset($value['credit_limit']) ? $value['credit_limit'] : '',
                    'motor_anticipated_business' => isset($value['motor_anticipated_business_1']) ? $value['motor_anticipated_business_1'] : '',
                    'motor_next_year_business' => isset($value['motor_next_year_business_1']) ? $value['motor_next_year_business_1'] : '',
                    'pump_anticipated_business' => isset($value['pump_anticipated_business_1']) ? $value['pump_anticipated_business_1'] : '',
                    'pump_next_year_business' => isset($value['pump_next_year_business_1']) ? $value['pump_next_year_business_1'] : '',
                    'F&A_anticipated_business' => isset($value['F&A_anticipated_business_1']) ? $value['F&A_anticipated_business_1'] : '',
                    'F&A_next_year_business' => isset($value['F&A_next_year_business_1']) ? $value['F&A_next_year_business_1'] : '',
                    'lighting_anticipated_business' => isset($value['lighting_anticipated_business_1']) ? $value['lighting_anticipated_business_1'] : '',
                    'lighting_next_year_business' => isset($value['lighting_next_year_business_1']) ? $value['lighting_next_year_business_1'] : '',
                    'agri_anticipated_business' => isset($value['agri_anticipated_business_1']) ? $value['agri_anticipated_business_1'] : '',
                    'agri_next_year_business' => isset($value['agri_next_year_business_1']) ? $value['agri_next_year_business_1'] : '',
                    'solar_anticipated_business' => isset($value['solar_anticipated_business_1']) ? $value['solar_anticipated_business_1'] : '',
                    'solar_next_year_business' => isset($value['solar_next_year_business_1']) ? $value['solar_next_year_business_1'] : '',
                    'manufacture_company_1' => isset($value['manufacture_company_1']) ? $value['manufacture_company_1'] : '',
                    'manufacture_product_1' => isset($value['manufacture_product_1']) ? $value['manufacture_product_1'] : '',
                    'manufacture_business_1' => isset($value['manufacture_business_1']) ? $value['manufacture_business_1'] : '',
                    'manufacture_turn_over_1' => isset($value['manufacture_turn_over_1']) ? $value['manufacture_turn_over_1'] : '',
                    'manufacture_company_2' => isset($value['manufacture_company_2']) ? $value['manufacture_company_2'] : '',
                    'manufacture_product_2' => isset($value['manufacture_product_2']) ? $value['manufacture_product_2'] : '',
                    'manufacture_business_2' => isset($value['manufacture_business_2']) ? $value['manufacture_business_2'] : '',
                    'manufacture_turn_over_2' => isset($value['manufacture_turn_over_2']) ? $value['manufacture_turn_over_2'] : '',
                    'approval_status' => isset($value['approval_status']) ? $value['approval_status'] : '0',
                    'bm_remark' => isset($value['bm_remark']) ? $value['bm_remark'] : '',
                    'certificate' => $value->getMedia('certificate')->count() > 0 && Storage::disk('s3')->exists($value->getMedia('certificate')[0]->getPath()) ? Storage::disk('s3')->url($value->getMedia('certificate')[0]->getPath()) : '',
                ];
            }
            return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $data], 200);
        }else{
            return response()->json(['status' => 'error', 'message' => 'Appointment not found.'], 404);
        }

    }

    public function getappointmentsPDF(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'appointment_id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], 400);
            }
            $data = [
                'dealerAppointment' => DealerAppointment::find($request->appointment_id),
                'kyc_ckeckbox' => config('constants.kyc_ckeckbox'),
            ];

            $html = view('dealer_appointment.pdf', $data)->render();
            $pdfDirectory = public_path('pdf/orders/');
            File::makeDirectory($pdfDirectory, $mode = 0755, true, true);
            $pdfFilePath = $pdfDirectory . 'Appointment_' . $request->appointment_id . '.pdf';

            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', true);
            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();

            file_put_contents($pdfFilePath, $dompdf->output());
            $data_main['pdf_url'] = $url = url(str_replace('/var/www/html/', '', $pdfFilePath));
            return response(['status' => 'Success', 'message' => 'Data retrieved successfully.', 'data' => $data_main], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function approveAppointment(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'appointment_id' => 'required',
                'status' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], 400);
            }
            $update = DealerAppointment::where('id', $request->appointment_id)->update(['approval_status' => $request->status, 'remark' => $request->remark, 'sales_approve' => $request->user()->id]);
            if ($update) {
                return response()->json(['status' => 'success', 'message' => 'Appointment status chnaged successfully !!']);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Somthing went wrong.']);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function addbmremark(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'appointment_id' => 'required',
                'bm_remark' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], 400);
            }
            $update = DealerAppointment::where('id', $request->appointment_id)->update(['bm_remark' => $request->bm_remark, 'bm_remark_user' => $request->user()->id]);
            if ($update) {
                return response()->json(['status' => 'success', 'message' => 'Appointment remark add successfully !!']);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Somthing went wrong.']);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
