<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingAddress extends Model
{
    use HasFactory;
    protected $table = 'shipping_addresses';

    protected $fillable = [ 'active', 'address1', 'address2', 'landmark', 'locality', 'customer_id', 'user_id', 'country_id', 'state_id', 'district_id', 'city_id', 'pincode_id', 'zipcode', 'created_by', 'deleted_at', 'created_at', 'updated_at','model_type','model_id'];

    protected $appends = ['full_address'];

    public function getFullAddressAttribute()
    {
        $parts = [
            $this->address1,
            $this->address2,
            $this->landmark,
            $this->locality,
            optional($this->cityname)->city_name,
            optional($this->districtname)->district_name,
            optional($this->statename)->state_name,
            optional($this->countryname)->country_name,
            $this->zipcode ?: optional($this->pincodename)->pincode,
        ];

        return implode(', ', array_filter($parts));
    }

    public function save_data($request)
    {
        try
        {
            
            $created_at = getcurentDateTime();
            $address = ShippingAddress::firstOrNew(array('customer_id' => $request['customer_id']));
            $address->active = 'Y';
            $address->customer_id = !empty($request['customer_id'])? $request['customer_id']:null;
            $address->address1 = !empty($request['shipping_address1'])? ucfirst($request['shipping_address1']):'';
            $address->address2 = !empty($request['shipping_address2'])? ucfirst($request['shipping_address2']):'';
            $address->landmark = !empty($request['shipping_landmark'])? ucfirst($request['shipping_landmark']):'';
            $address->locality = !empty($request['shipping_locality'])? $request['shipping_locality']:'';
            $address->user_id = !empty($request['shipping_user_id'])? $request['shipping_user_id']:null;
            $address->country_id = !empty($request['shipping_country_id'])? $request['shipping_country_id']:null;
            $address->state_id = !empty($request['shipping_state_id'])? $request['shipping_state_id']:null;
            $address->district_id = !empty($request['shipping_district_id'])? $request['shipping_district_id']:null;
            $address->city_id = !empty($request['shipping_city_id'])? $request['shipping_city_id']:null;
            $address->pincode_id = !empty($request['shipping_pincode_id'])? $request['shipping_pincode_id']:null;
            $address->zipcode = !empty($request['shipping_zipcode'])? $request['shipping_zipcode']:'';
            if($address === null)
            {
                $address->created_at = $created_at;
            }
            $address->updated_at = $created_at;
            if($address->save())
            {
                return $response = array('status' => 'success', 'message' => 'Address Insert Successfully','address_id' => $address->id);
            }
            return $response = array('status' => 'error', 'message' => 'Error in Address Store');
        }
        catch(\Exception $e)
        {
            return $response = array('status' => 'error', 'message' => $e->getMessage());
        }
    }

    public function countryname()
    {
        return $this->belongsTo('App\Models\Country', 'country_id', 'id')->select('id', 'country_name');
    }
    public function statename()
    {
        return $this->belongsTo('App\Models\State', 'state_id', 'id')->select('id', 'state_name');
    }
    public function districtname()
    {
       return $this->belongsTo('App\Models\District', 'district_id', 'id')->select('id', 'district_name');
    }
    public function cityname()
    {
        return $this->belongsTo('App\Models\City', 'city_id', 'id')->select('id', 'city_name');
    }
    public function pincodename()
    {
        return $this->belongsTo('App\Models\Pincode', 'pincode_id', 'id')->select('id', 'pincode');
    }

    public function customer()
    {
        $this->belongsTo(Customers::class, 'customer_id', 'id');
    }

}
