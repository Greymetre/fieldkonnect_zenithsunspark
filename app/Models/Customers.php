<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Support\Facades\Hash;

use Laravel\Passport\Token;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Customers extends Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $table = 'customers';

    protected $fillable = ['active', 'name', 'first_name', 'last_name', 'mobile', 'email', 'password', 'notification_id', 'latitude', 'longitude', 'device_type', 'gender', 'profile_image', 'shop_image', 'customer_code', 'status_id', 'region_id', 'customertype', 'firmtype', 'created_by', 'updated_by', 'executive_id', 'otp', 'deleted_at', 'created_at', 'updated_at', 'beatscheduleid', 'same_address', 'manager_name', 'manager_phone', 'contact_number', 'parent_id', 'sap_code'];
    protected $appends = ['full_address'];

    public function message()
    {
        return [
            'name.required' => 'Enter Firm Name',
        ];
    }

    /**
     * Route notifications for the Apn channel.
     *
     * @return string|array
     */
    public function routeNotificationForApn()
    {
        return $this->customerdetails->fcm_token ?? '';
    }

    /**
     * Route notifications for the Fcm channel.
     *
     * @return string|array
     */
    public function routeNotificationForFcm()
    {
        return $this->customerdetails->fcm_token ?? '';
    }

    public function insertrules()
    {

        return [
            'name' => 'required|string|regex:/[a-zA-Z0-9\s]+/',
            'email' => 'nullable|email|unique:customers,email',
            'mobile'        => 'required|min:6666666666|max:9999999999|numeric|unique:customers,mobile',
        ];
    }
    public function updaterules($id = '')
    {
        return [
            'name' => 'required|string|regex:/[a-zA-Z0-9\s]+/',
            //'email' => 'email|unique:customers,email',
            //'mobile' => 'required|numeric|unique:customers,mobile,'.$id,
        ];
    }

    public function save_data($request)
    {
        try {
            $created_at = getcurentDateTime();
            if (strlen(preg_replace('/\s+/', '', $request['mobile'])) == 10) {
                $request['mobile'] = '91' . preg_replace('/\s+/', '', $request['mobile']);
            }
            if ($customer_id = Customers::insertGetId([
                'active' => 'Y',
                'name' => !empty($request['name']) ? ucfirst($request['name']) : '',
                'first_name' => !empty($request['first_name']) ? ucfirst($request['first_name']) : '',
                'last_name' => !empty($request['last_name']) ? ucfirst($request['last_name']) : '',
                'mobile' => $request['mobile'],
                'email' => !empty($request['email']) ? $request['email'] : null,
                'working_status' => !empty($request['working_status']) ? $request['working_status'] : null,
                'creation_date' => !empty($request['creation_date']) ? $request['creation_date'] : null,
                'sap_code' => !empty($request['sap_code']) ? $request['sap_code'] : null,
                'password' => !empty($request['password']) ? Hash::make($request['password']) : '',
                'contact_number' => !empty($request['contact_number']) ? $request['contact_number'] : '',
                'notification_id' => !empty($request['notification_id']) ? $request['notification_id'] : '',
                'latitude' => !empty($request['latitude']) ? $request['latitude'] : null,
                'same_address' => !empty($request['same_address']) ? $request['same_address'] : 0,
                'longitude' => !empty($request['longitude']) ? $request['longitude'] : null,
                'device_type' => !empty($request['device_type']) ? ucfirst($request['device_type']) : '',
                'gender' => !empty($request['gender']) ? ucfirst($request['gender']) : '',
                'customer_code' => !empty($request['customer_code']) ? $request['customer_code'] : '',
                'profile_image' =>  !empty($request['profile_image']) ? $request['profile_image'] : '',
                'shop_image' =>  !empty($request['shop_image']) ? $request['shop_image'] : '',
                'status_id' =>  !empty($request['status_id']) ? $request['status_id'] : 2,
                'customertype' =>  !empty($request['customertype']) ? $request['customertype'] : 1,
                'firmtype' =>  !empty($request['firmtype']) ? $request['firmtype'] : null,
                //'executive_id' =>  !empty($request['executive_id'])? $request['executive_id'] : $request['created_by'],
                //'parent_id' =>  !empty($request['parent_id'])? $request['parent_id'] : null,
                'created_by' =>  !empty($request['created_by']) ? $request['created_by'] : null,
                'manager_name' => !empty($request['manager_name']) ? $request['manager_name'] : '',
                'manager_phone' => !empty($request['manager_phone']) ? $request['manager_phone'] : '',
                'created_at' => $created_at,
                'updated_at' => $created_at
            ])) {
                if (isset($request['customertype']) && $request['customertype'] == 4) {
                    $mobile = substr($request['mobile'], 2); // remove country code
                    Customers::where('id', $customer_id)->update([
                        'password' => Hash::make($mobile)
                    ]);
                }
                return $response = array('status' => 'success', 'message' => 'Customer Insert Successfully', 'customer_id' => $customer_id);
            }
            return $response = array('status' => 'error', 'message' => 'Error in Customer Store');
        } catch (\Exception $e) {
            return $response = array('status' => 'error', 'message' => $e->getMessage());
        }
    }

    public function update_data($request)
    {
        try {
            if (strlen(preg_replace('/\s+/', '', $request['mobile'])) == 10) {
                $request['mobile'] = '+91' . preg_replace('/\s+/', '', $request['mobile']);
            }
            $customers = Customers::find($request['customer_id']);
            $customers->name = !empty($request['name']) ? $request['name'] : '';
            $customers->first_name = !empty($request['first_name']) ? ucfirst($request['first_name']) : '';
            $customers->working_status = !empty($request['working_status']) ? $request['working_status'] : null;
            $customers->creation_date = !empty($request['creation_date']) ? $request['creation_date'] : null;
            $customers->sap_code = !empty($request['sap_code']) ? $request['sap_code'] : null;
            $customers->last_name = !empty($request['last_name']) ? ucfirst($request['last_name']) : '';
            $customers->gender = !empty($request['gender']) ? ucfirst($request['gender']) : '';
            $customers->customer_code = !empty($request['customer_code']) ? $request['customer_code'] : '';
            $customers->customertype =  !empty($request['customertype']) ? $request['customertype'] : $customers->customertype;
            $customers->firmtype = !empty($request['firmtype']) ? $request['firmtype'] : null;
            $customers->same_address = !empty($request['same_address']) ? $request['same_address'] : 0;
            //$customers->executive_id = !empty($request['executive_id'])? $request['executive_id']:null;

            if (isset($request['password'])) {
                $customers->password = !empty($request['password']) ? Hash::make($request['password']) : '';
            }
            if (!empty($request['mobile'])) {
                $customers->mobile = $request['mobile'];
            }

            if (!empty($request['contact_number'])) {
                $customers->contact_number = $request['contact_number'];
            }

            //  if(!empty($request['parent_id']))
            // {
            //     $customers->parent_id = $request['parent_id'];
            // }

            if (!empty($request['email'])) {
                $customers->email = !empty($request['email']) ? $request['email'] : null;
            }
            if (!empty($request['profile_image'])) {
                $customers->profile_image = !empty($request['profile_image']) ? $request['profile_image'] : '';
            }
            if (!empty($request['shop_image'])) {
                $customers->shop_image = !empty($request['shop_image']) ? $request['shop_image'] : '';
            }
            if (!empty($request['manager_name'])) {
                $customers->manager_name = !empty($request['manager_name']) ? $request['manager_name'] : '';
            }
            if (!empty($request['manager_phone'])) {
                $customers->manager_phone = !empty($request['manager_phone']) ? $request['manager_phone'] : '';
            }
            if (!empty($request['latitude']) && !empty($request['longitude'])) {
                $customers->latitude = !empty($request['latitude']) ? $request['latitude'] : null;
                $customers->longitude = !empty($request['longitude']) ? $request['longitude'] : null;
            }
            $customers->updated_at = getcurentDateTime();
            if ($customers->save()) {
                return $response = array('status' => 'success', 'message' => 'User Update Successfully');
            }
            return $response = array('status' => 'error', 'message' => 'Error in User Profile Update');
        } catch (\Exception $e) {
            return $response = array('status' => 'error', 'message' => $e->getMessage());
        }
    }
    public function createdbyname()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    public function employeename()
    {
        return $this->belongsTo('App\Models\User', 'executive_id', 'id')->select('id', 'name');
    }

    public function userdetails()
    {
        return $this->belongsTo('App\Models\User', 'executive_id', 'id');
    }


    public function customertypes()
    {
        return $this->belongsTo('App\Models\CustomerType', 'customertype', 'id')->select('id', 'customertype_name');
    }

    public function firmtypes()
    {
        return $this->belongsTo('App\Models\FirmType', 'firmtype', 'id')->select('id', 'firmtype_name');
    }

    public function customerdetails()
    {
        return $this->belongsTo('App\Models\CustomerDetails', 'id', 'customer_id');
    }

    public function customeraddress()
    {
        return $this->belongsTo('App\Models\Address', 'id', 'customer_id')->select('id', 'address1', 'address2', 'landmark', 'locality', 'customer_id', 'user_id', 'country_id', 'state_id', 'district_id', 'city_id', 'pincode_id', 'zipcode');
    }

    public function customershippingaddress()
    {
        return $this->belongsTo('App\Models\ShippingAddress', 'id', 'customer_id')->select('id', 'address1', 'address2', 'landmark', 'locality', 'customer_id', 'user_id', 'country_id', 'state_id', 'district_id', 'city_id', 'pincode_id', 'zipcode');
    }
    public function addresslists()
    {
        return $this->hasMany('App\Models\Address', 'customer_id', 'id')->select('id', 'address1', 'address2', 'landmark', 'locality', 'customer_id', 'user_id', 'country_id', 'state_id', 'district_id', 'city_id', 'pincode_id');
    }

    public function customerdocuments()
    {
        return $this->hasMany('App\Models\Attachment', 'customer_id', 'id')->select('customer_id', 'file_path', 'document_name');
    }

    public function statusname()
    {
        return $this->belongsTo('App\Models\Status', 'status_id', 'id')->select('id', 'status_name');
    }

    public function beatdetails()
    {
        return $this->belongsTo('App\Models\BeatCustomer', 'id', 'customer_id')->select('beat_id', 'customer_id');
    }

    public function surveys()
    {
        return $this->hasMany('App\Models\SurveyData', 'customer_id', 'id')->select('field_id', 'customer_id', 'value');
    }
    public function visitsinfo()
    {
        return $this->hasMany('App\Models\VisitReport', 'customer_id', 'id')->orderBy('created_at', 'desc');
    }

    public function customerdeals()
    {
        return $this->hasMany('App\Models\DealIn', 'customer_id', 'id')->select('customer_id', 'types', 'hcv', 'mav', 'lmv', 'lcv', 'other', 'tractor');
    }

    // For get parent 
    public function parentdetail()
    {
        return $this->belongsTo(Customers::class, 'parent_id', 'id');
    }


    public function getemployeedetail()
    {
        return $this->hasMany(EmployeeDetail::class, 'customer_id', 'id');
    }


    public function getparentdetail()
    {
        return $this->hasMany(ParentDetail::class, 'customer_id', 'id');
    }

    public function customer_transacation()
    {
        return $this->hasMany(TransactionHistory::class, 'customer_id', 'id');
    }

    public function getretailers()
    {
        return $this->hasMany(ParentDetail::class, 'parent_id', 'id');
    }

    public function getFullAddressAttribute()
    {
        if (!$this->customeraddress) {
            return null;
        }

        $address1 = $this->customeraddress->address1 ?? '';
        $address2 = $this->customeraddress->address2 ?? '';
        $city = optional($this->customeraddress->cityname)->city_name ?? '';
        $district = optional($this->customeraddress->districtname)->district_name ?? '';
        $state = optional($this->customeraddress->statename)->state_name ?? '';
        $pincode = optional($this->customeraddress->pincodename)->pincode ?? '';
        $zipcode = $this->customeraddress->zipcode ?? '';

        $fullAddress = trim("$address1, $address2, $city, $district, $state - $zipcode,$pincode", ', -');

        return $fullAddress ?: null;
    }

    public function transactions()
    {
        return $this->hasMany(TransactionHistory::class, 'customer_id', 'id');
    }

    public function redemptions()
    {
        return $this->hasMany(Redemption::class, 'customer_id', 'id');
    }

    protected static function booted()
    {
        static::updating(function ($customer) {
            if ($customer->isDirty('active') && $customer->active === 'N') {
                // Revoke all tokens for this customer
                Token::where('user_id', $customer->id)->update(['revoked' => true]);
            }
        });
    }
}
