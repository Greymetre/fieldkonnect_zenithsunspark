<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlannedSopSaleData extends Model
{
    use HasFactory;

    protected $fillable = [
        'planned_sop_id',
        'month_1',
        'month_2',
        'month_3',
        'month_4',
        'month_5',
        'month_6',
        'month_7',
        'month_8',
        'month_9',
        'month_10',
        'month_11',
        'month_12',
        'min',
        'max',
        'avg',
    ];
}
