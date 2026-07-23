<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckInDraft extends Model
{
    use HasFactory;

    protected $fillable = ['checkin_id', 'draft_msg', 'created_at', 'updated_at'];

    public $timestamps = true;
}
