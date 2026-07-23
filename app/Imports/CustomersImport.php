<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Customers;
use App\Models\CustomerDetails;
use App\Models\Address;
use App\Models\Attachment;
use App\Models\City;
use App\Models\Pincode;
use App\Models\UserDetails;
use App\Models\Beat;
use App\Models\BeatCustomer;
use App\Models\BeatSchedule;
use App\Models\EmployeeDetail;
use App\Models\ParentDetail;

use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithProgressBar;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Illuminate\Support\Facades\DB;
use Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class CustomersImport implements ToCollection, WithValidation, WithHeadingRow, WithBatchInserts, WithChunkReading
{
  use Importable, SkipsFailures;

  public function model(array $row)
  {
    return new Customers([
      //
    ]);
  }

  public function collection(Collection $rows)
  {
    $customerdetails = collect([]);
    $addressdetails = collect([]);
    $attachments = collect([]);
     
    foreach ($rows as $ky=>$row) {
      if (isset($row['mobile']) && strlen(preg_replace('/\s+/', '', $row['mobile'])) == 10) {
        $row['mobile'] = '91' . preg_replace('/\s+/', '', $row['mobile']);
      }

      if (isset($row['creation_date']) && is_numeric($row['creation_date'])) {
        $excelDate = $row['creation_date'] - 25569; // Adjust for Excel's epoch
        $unixTimestamp = strtotime('+' . $excelDate . ' days', strtotime('1970-01-01'));
        $row['creation_date'] = !empty($row['creation_date']) ? Carbon::createFromTimestamp($unixTimestamp)->toDateString() : '';
      }


      if (!empty($row['customer_id'])) {

        Customers::where('id', '=', $row['customer_id'])->update([
          'name' => $row['firm_name'],
          'active' => $row['status'] ?? 'Y',
          'first_name' => !empty($row['first_name']) ? $row['first_name'] : '',
          'last_name' => !empty($row['last_name']) ? $row['last_name'] : '',
          'contact_number' => !empty($row['contact_number2']) ? $row['contact_number2'] : null,
          //'executive_id' => !empty($row['employee_id'])? $row['employee_id'] :null,
          //'parent_id' => !empty($row['parent_id'])? $row['parent_id'] :null,
          'customer_code' => !empty($row['customer_code']) ? $row['customer_code'] : null,
          'email' => !empty($row['email']) ? $row['email'] : null,
          'working_status' => !empty($row['working_status']) ? $row['working_status'] : null,
          'creation_date' => !empty($row['creation_date']) ? $row['creation_date'] : null,
          'sap_code' => !empty($row['sap_code']) ? $row['sap_code'] : null,
          'customertype' => !empty($row['customer_type_id']) ? $row['customer_type_id'] : null,


        ]);



        CustomerDetails::where('customer_id', '=', $row['customer_id'])->update([
          'gstin_no' => !empty($row['gstin_no']) ? $row['gstin_no'] : null,
          'pan_no' => !empty($row['pan_no']) ? $row['pan_no'] : null,
          'aadhar_no' => !empty($row['aadhar_no']) ? $row['aadhar_no'] : null,
          'otherid_no' => !empty($row['other_no']) ? $row['other_no'] : null,
          'grade' => !empty($row['grade']) ? $row['grade'] : null,
          'visit_status' => !empty($row['visit_status']) ? $row['visit_status'] : null,

        ]);


        Address::where('customer_id', '=', $row['customer_id'])->update([
          'pincode_id' => !empty($row['pincode_id']) ? $row['pincode_id'] : null,
          'city_id' => !empty($row['city_id']) ? $row['city_id'] : null,
          'district_id' => !empty($row['district_id']) ? $row['district_id'] : null,
          'state_id' => !empty($row['state_id']) ? $row['state_id'] : null,
          'address1' => !empty($row['address']) ? $row['address'] : null,
          'landmark' => !empty($row['market_place']) ? $row['market_place'] : null,

        ]);



        //employee start

        if (!empty($row['employee_id'])) {

          EmployeeDetail::where('customer_id', $row['customer_id'])->delete();
          //$row['employee_id'] = str_replace('[','',$row['employee_id']);
          //$row['employee_id'] = str_replace(']','',$row['employee_id']);
          $employee_data = explode(",", $row['employee_id']);

          foreach ($employee_data as $keys => $row_employee) {
            $employeeDetail = EmployeeDetail::updateOrCreate(
              [
                'customer_id' => $row['customer_id'],
                'user_id' => $row_employee,
                'created_by' => Auth::user()->id,
              ]

            );
          }
        }

        // employee end

        //parent start

        if (!empty($row['parent_id'])) {
          ParentDetail::where('customer_id', $row['customer_id'])->delete();
          //$row['parent_id'] = str_replace('[','',$row['parent_id']);
          //$row['parent_id'] = str_replace(']','',$row['parent_id']);

          $parent_data = explode(",", $row['parent_id']);

          foreach ($parent_data as $key => $row_parent) {
            $parentDetail = ParentDetail::updateOrCreate(
              [
                'customer_id' => $row['customer_id'],
                'parent_id' => $row_parent,
                'created_by' => Auth::user()->id,
              ]
            );
          }
        }
        // parent end  

      } else {


        if ($customer = Customers::updateOrCreate(['mobile' =>  !empty($row['mobile']) ? (string)$row['mobile'] : '',],[
          'active' => 'Y',
          'name' => !empty($row['firm_name']) ? ucfirst($row['firm_name']) : '',
          'first_name' => !empty($row['first_name']) ? ucfirst($row['first_name']) : '',
          'last_name' => !empty($row['last_name']) ? ucfirst($row['last_name']) : '',
          'email' => !empty($row['email']) ? $row['email'] : null,
          'working_status' => !empty($row['working_status']) ? $row['working_status'] : null,
          'creation_date' => !empty($row['creation_date']) ? $row['creation_date'] : null,
          'sap_code' => !empty($row['sap_code']) ? $row['sap_code'] : null,
          'password' => !empty($row['password']) ? Hash::make($row['password']) : '',
          'notification_id' => !empty($row['notification_id']) ? $row['notification_id'] : '',
          'latitude' => !empty($row['latitude']) ? $row['latitude'] : '',
          'longitude' => !empty($row['longitude']) ? $row['longitude'] : '',
          'device_type' => !empty($row['device_type']) ? ucfirst($row['device_type']) : '',
          'gender' => !empty($row['gender']) ? ucfirst($row['gender']) : '',
          'customer_code' => !empty($row['customer_code']) ? $row['customer_code'] : null,
          'profile_image' =>  !empty($row['profile_image']) ? $row['profile_image'] : '',
          'status_id' =>  !empty($row['status_id']) ? $row['status_id'] : 2,
          'customertype' =>  !empty($row['customertype']) ? $row['customertype'] : 1,
          'firmtype' =>  !empty($row['firmtype']) ? $row['firmtype'] : null,
          // 'created_by' => $user_id,
          'created_by' => Auth::user()->id,
          //'executive_id' => $executive_id,
          //'parent_id' => $parent_id,
          'contact_number' => (string)!empty($row['contact_number']) ? $row['contact_number'] : null,
          'created_at' => getcurentDateTime(),
          'updated_at' => getcurentDateTime()
        ])) {

          //employee start
          if (!empty($row['employee_id'])) {
            //$row['employee_id'] = str_replace('[','',$row['employee_id']);
            //$row['employee_id'] = str_replace(']','',$row['employee_id']);
            $employee_data = explode(",", $row['employee_id']);

            foreach ($employee_data as $keys => $row_employee) {

              $employeeDetail = EmployeeDetail::updateOrCreate(
                [
                  'customer_id' => $customer['id'],
                  'user_id' => $row_employee,
                  'created_by' => Auth::user()->id,
                ]

              );
            }
          }

          //employee end



          //parent start

          if (!empty($row['parent_id'])) {
            //$row['parent_id'] = str_replace('[','',$row['parent_id']);
            //$row['parent_id'] = str_replace(']','',$row['parent_id']);

            $parent_data = explode(",", $row['parent_id']);

            foreach ($parent_data as $key => $row_parent) {
              $parentDetail = ParentDetail::updateOrCreate(
                [
                  'customer_id' => $customer['id'],
                  'parent_id' => $row_parent,
                  'created_by' => Auth::user()->id,
                ]
              );
            }
          }


          $pincode = Pincode::where('pincode', '=', $row['pincode_id'])->select('id', 'city_id')->first();
          $addressdetails->push([
            'active' => 'Y',
            'customer_id' => $customer['id'],
            'address1' => !empty($row['address1']) ? $row['address1'] : '',
            'address2' => !empty($row['address2']) ? $row['address2'] : '',
            'landmark' => !empty($row['landmark']) ? $row['landmark'] : '',
            'locality' => !empty($row['locality']) ? $row['locality'] : '',
            'country_id' => !empty($row['country_id']) ? $row['country_id'] : null,
            'state_id' => !empty($row['state_id']) ? $row['state_id'] : null,
            // 'district_id' => !empty($city['district_id'])? $city['district_id']:null,
            'district_id' => !empty($row['district_id']) ? $row['district_id'] : null,
            // 'city_id' => !empty($pincode['city_id'])? $pincode['city_id']:null,
            // 'pincode_id' => !empty($pincode['id'])? $pincode['id']:null,
            'city_id' => !empty($row['city_id']) ? $row['city_id'] : null,
            'pincode_id' => !empty($row['pincode_id']) ? $row['pincode_id'] : null,
            'created_by' => Auth::user()->id,
            'created_at' => getcurentDateTime(),
            'updated_at' => getcurentDateTime()
          ]);
          // dd($customerdetails, $customer);
          if ($customerdetails->isNotEmpty()) {
            CustomerDetails::updateOrCreate(['customer_id' => $customer['id'],],[
              'active' => 'Y',
              // 'gstin_no' => !empty($row['gstin_no'])? $row['gstin_no']:null,
              // 'pan_no' => !empty($row['pan_no'])? $row['pan_no']:null,
              // 'aadhar_no' => !empty($row['aadhar_no'])? $row['aadhar_no']:null,
              // 'otherid_no' => !empty($row['otherid_no'])? $row['otherid_no']:null,
              'enrollment_date' => !empty($row['enrollment_date']) ? $row['enrollment_date'] : null,
              'approval_date' => !empty($row['approval_date']) ? $row['approval_date'] : null,
              'created_at' => getcurentDateTime(),
              'updated_at' => getcurentDateTime()
            ]);
          }
          if ($addressdetails->isNotEmpty()) {
            Address::insert($addressdetails->toArray());
          }
          // if ($attachments->isNotEmpty()) {
          //   Attachment::insert($attachments->toArray());
          // }
        }
      }
    }
  }

  public function rules(): array
  {
    return [
      //'name' => 'required|string|regex:/[a-zA-Z0-9\s]+/',
    ];
  }

  public function batchSize(): int
  {
    return 1000;
  }

  public function chunkSize(): int
  {
    return 1000;
  }

  public function onFailure(Failure ...$failures)
  {
    Log::stack(['import-failure-logs'])->info(json_encode($failures));
  }
}
