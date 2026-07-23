<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadNotification extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'model_id',
        'user_id',
        'title',
        'model',
        'body',
        'read',
    ];

    /**
     * The lead this notification belongs to.
     */
    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    /**
     * The user who will receive or has received the notification.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
