<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    use HasFactory;

    protected $table = 'attachments';

    protected $fillable = [ 'active', 'product_id', 'user_id', 'customer_id', 'order_id', 'sales_id', 'file_path', 'document_name', 'created_at', 'updated_at'];

    public function customer()
    {
        return $this->belongsTo(Customers::class, 'customer_id'); // Ensure the foreign key is correct
    }
}
