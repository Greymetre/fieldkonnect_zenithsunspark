<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrimaryScheme extends Model
{
    use HasFactory;

    protected $fillable = [ 'active', 'scheme_name', 'scheme_description', 'start_date', 'end_date', 'customer_type', 'scheme_type', 'scheme_basedon', 'assign_to', 'branch', 'state', 'customer', 'division','minimum', 'maximum','created_at', 'updated_at','repetition','week_days','year_months','quarter'];


    public function primaryscheme_details()
    {
        return $this->hasMany('App\Models\PrimarySchemeDetail','primary_scheme_id','id')->where('active','Y');
    }
}
