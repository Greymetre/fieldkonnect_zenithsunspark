<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesOrderDispatch extends Model
{
    protected $fillable = ['dispatch_number', 'sales_order_id', 'dispatch_date', 'remark', 'dispatched_by'];
    protected $casts = ['dispatch_date' => 'date'];
    public function items() { return $this->hasMany(SalesOrderDispatchItem::class); }
    public function salesOrder() { return $this->belongsTo(SalesOrder::class); }

    public function returnData(): array
    {
        if (!str_starts_with((string) $this->dispatch_number, 'RTR-')) {
            return [];
        }

        $data = json_decode((string) $this->remark, true);
        return is_array($data) ? $data : [];
    }

    public function isAcceptedReturn(): bool
    {
        return ($this->returnData()['return_status'] ?? null) === 'accepted';
    }
}
