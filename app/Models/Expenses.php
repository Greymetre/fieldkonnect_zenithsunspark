<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Auth;

class Expenses extends Model implements HasMedia
{
  use HasFactory, InteractsWithMedia;

  protected $fillable = ['expenses_type', 'user_id', 'date', 'claim_amount', 'start_km', 'stop_km', 'total_km', 'note', 'checker_status', 'accountant_status', 'created_at', 'updated_at', 'created_by', 'approve_amount', 'reason', 'approve_reject_by'];

  protected $appends = ['is_self'];
  // public function expense_type(){
  //     return $this->hasOne(ExpensesType::class);
  // }


  public function expense_type()
  {
    return $this->belongsTo(ExpensesType::class, 'expenses_type', 'id');
  }

  public function users()
  {
    return $this->belongsTo(User::class, 'user_id', 'id');
  }

  public function registerMediaCollections(): void
  {
    $this->addMediaCollection('expense_file')
      ->useFallbackUrl(asset(config('constants.NO_IMAGE_URL')))
      ->useFallbackPath(public_path(config('constants.NO_IMAGE_URL')));
    //->singleFile();
  }

  public function approve_reject()
  {
    return $this->belongsTo(User::class, 'approve_reject_by', 'id');
  }

  public function get_time_history()
  {
    return $this->hasMany('App\Models\ExpenseLog', 'expense_id', 'id');
  }

  public function isSelf(): Attribute
  {
    return Attribute::get(function () {
      return $this->user_id === Auth::id();
    });
  }
}
