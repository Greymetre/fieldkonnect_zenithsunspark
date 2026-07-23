<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitReport extends Model
{
    use HasFactory;

    protected $table = 'visit_reports';

    protected $fillable = ['active', 'checkin_id', 'user_id','customer_id', 'visit_type_id', 'report_title', 'description', 'created_by', 'deleted_at', 'created_at', 'updated_at'];

    public function createdbyname()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id')->select('id','name');
    }

    public function users()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id')->select('id','name','mobile');
    }

    public function customers()
    {
        return $this->belongsTo('App\Models\Customers', 'customer_id', 'id');
    }

    public function visittypename()
    {
        return $this->belongsTo('App\Models\VisitType', 'visit_type_id', 'id')->select('id','type_name');
    }

    public function customeraddress()
    {
        return $this->belongsTo('App\Models\Address', 'customer_id', 'customer_id')->select('id','address1', 'address2', 'landmark', 'locality', 'customer_id', 'user_id', 'country_id', 'state_id','district_id' ,'city_id', 'pincode_id','zipcode');
    }
}
