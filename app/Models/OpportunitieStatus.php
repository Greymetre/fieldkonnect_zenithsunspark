<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class OpportunitieStatus extends Model
{
    use HasFactory;

    protected $table = 'opportunitie_statuses';

    protected $fillable = [
        'status_name',
        'ordering',
        'created_by',
    ];

    public function createbyname(){
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    
}
