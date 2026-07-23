<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Carbon\Carbon;

class WarrantyActivation extends Model implements HasMedia
{
    use HasFactory,InteractsWithMedia;

    protected $fillable = ['product_serail_number','product_id','branch_id','end_user_id','customer_id','sale_bill_no','sale_bill_date','warranty_date','status','created_by','created_at','updated_at'];

    public $timestamps = true;

    public function product_details()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function seller_details()
    {
        return $this->belongsTo(Customers::class, 'customer_id', 'id');
    }

    public function customer()
    {
        return $this->belongsTo(EndUser::class, 'end_user_id', 'id');
    }

    public function branch_details()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }

    public function createdByName()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function registerMediaCollections(): void {
        $this->addMediaCollection('warranty_activation_attach')
             ->useFallbackUrl(asset(config('constants.NO_IMAGE_URL')))
             ->useFallbackPath(public_path(config('constants.NO_IMAGE_URL')))
             ->singleFile();
    }

     public function getWarrantyDateAttribute($value)
    {
        try {
            return $value ? Carbon::parse($value)->format('d-m-Y') : '';
        } catch (\Exception $e) {
            return ''; // Return null if parsing fails
        }
    }
}
