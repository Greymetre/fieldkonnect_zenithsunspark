<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplaintTimeline extends Model
{
    use HasFactory;

    protected $table = 'complaint_timelines';

    protected $fillable = [ 'complaint_id', 'created_by', 'status', 'remark', 'created_at', 'updated_at'];
    public $timestamps = true;


    public function created_by_details()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
