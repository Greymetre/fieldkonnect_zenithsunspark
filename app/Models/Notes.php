<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notes extends Model
{
    use HasFactory;

    protected $table = 'notes';

    protected $fillable = [ 'active', 'user_id', 'customer_id', 'note', 'purpose', 'status_id', 'callstatus', 'created_at', 'updated_at'];

    public function statusname()
    {
        return $this->belongsTo('App\Models\Status', 'status_id', 'id')->select('id','status_name','display_name');
    }

    public function customerinfo()
    {
        return $this->belongsTo('App\Models\Customers', 'customer_id', 'id')->select('id','name','mobile','executive_id','customertype');
    }

    public function users()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id')->select('id','name');
    }

    public function customeraddress()
    {
        return $this->belongsTo('App\Models\Address', 'customer_id', 'customer_id')->select('id','state_id','district_id' ,'city_id');
    }
}
