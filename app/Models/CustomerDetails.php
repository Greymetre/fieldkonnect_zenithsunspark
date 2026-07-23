<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerDetails extends Model
{
    use HasFactory;

    protected $table = 'customer_details';

    protected $fillable = [  'active', 'customer_id', 'gstin_no', 'pan_no', 'aadhar_no', 'account_holder', 'account_number', 'bank_name', 'ifsc_code', 'otherid_no', 'enrollment_date', 'approval_date', 'shop_image', 'visiting_card', 'grade', 'visit_status', 'fcm_token', 'deleted_at', 'created_at', 'updated_at'];

    public function save_data($request)
    {
        try
        {

            $customer = CustomerDetails::firstOrNew(array('customer_id' => $request['customer_id']));
            $customer->active = 'Y';
            $customer->customer_id = isset($request['customer_id'])? $request['customer_id']:null;
            $customer->gstin_no = !empty($request['gstin_no'])? ucfirst($request['gstin_no']):null;
            $customer->pan_no = !empty($request['pan_no'])? ucfirst($request['pan_no']):null;
            $customer->aadhar_no = !empty($request['aadhar_no'])? ucfirst($request['aadhar_no']):null;
            $customer->account_holder = !empty($request['account_holder'])? ucfirst($request['account_holder']):null;
            $customer->account_number = !empty($request['account_number'])? $request['account_number']:null;
            $customer->bank_name = !empty($request['bank_name'])? $request['bank_name']:null;
            $customer->ifsc_code = !empty($request['ifsc_code'])? $request['ifsc_code']:null;
            $customer->otherid_no = !empty($request['otherid_no'])? $request['otherid_no']:null;
            $customer->enrollment_date = isset($request['enrollment_date'])? $request['enrollment_date']:null;
            $customer->approval_date = isset($request['approval_date'])? $request['approval_date']:null;
            $customer->visit_status = !empty($request['visit_status']) ? $request['visit_status'] : null;
            $customer->grade = !empty($request['grade']) ? $request['grade'] : null;
            $customer->created_at = getcurentDateTime();
            if($customer->save())
            {
                return $response = array('status' => 'success', 'message' => 'Profile Update Successfully');
            }
            return $response = array('status' => 'error', 'message' => 'Error in Profile Update');
        }
        catch(\Exception $e)
        {
            return $response = array('status' => 'error', 'message' => $e->getMessage());
        }
    }

    public function customer()
    {
        return $this->belongsTo(Customers::class, 'customer_id', 'id');
    }

    public function document_status_by()
    {
        return $this->belongsTo(User::class, 'status_update_by', 'id');
    }
}
