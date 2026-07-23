<?php

namespace App\Imports;

use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\UserDetails;
use App\Models\Product;
use App\Models\Customers;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithProgressBar;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Illuminate\Support\Facades\DB;
use Log;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class OrderImport implements ToCollection,WithValidation,WithHeadingRow, WithBatchInserts , WithChunkReading
{
    use Importable, SkipsFailures;

    

    public function model(array $row)
    {
        return new Order([
            //
        ]);
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            
            $user_id = UserDetails::where('employee_code','=',$row['emp_code'])->pluck('user_id')->first();
            $row['buyer_id'] = !empty($row['buyer_id']) ? $row['buyer_id'] : Customers::where('mobile','=',$row['buyer_code'])->pluck('id')->first();
            $row['seller_id'] = !empty($row['seller_id']) ? $row['seller_id'] : Customers::where('mobile','=',$row['seller_code'])->pluck('id')->first();
            $row['product_id'] = isset($row['product_id']) ? $row['product_id'] : Product::where('product_name','=',$row['product'])->pluck('id')->first();
            $order = Order::where('orderno', '=', $row['orderno'])->first();
            if ($order === null) {
                $order = Order::create([
                    
                    'active' => 'Y',
                    'buyer_id' => isset($row['buyer_id'])? $row['buyer_id']:null,
                    'seller_id' => isset($row['seller_id'])? $row['seller_id']:null,
                    'total_qty' => isset($row['total_qty'])? $row['total_qty']:$row['quantity'],
                    'shipped_qty' => isset($row['shipped_qty'])? $row['shipped_qty']:0,
                    'orderno' => isset($row['orderno'])? $row['orderno']:null,
                    'order_date' => isset($row['order_date'])? date('Y-m-d',strtotime($row['order_date'])):null,
                    'completed_date' => isset($row['completed_date'])? date('Y-m-d',strtotime($row['completed_date']) ):null,
                    'total_gst' => !empty($row['total_gst'])? $row['total_gst']:0.00,
                    'total_discount' => isset($row['total_discount'])? $row['total_discount']:0.00,
                    'extra_discount' => isset($row['extra_discount'])? $row['extra_discount']:0.00,
                    'extra_discount_amount' => isset($row['extra_discount_amount'])? $row['extra_discount_amount']:0.00,
                    'sub_total' => !empty($row['sub_total'])? $row['sub_total']:$row['amount'],
                    'grand_total' => !empty($row['grand_total'])? $row['grand_total']:$row['amount'],
                    'status_id' => isset($row['status_id'])? $row['status_id']:null,
                    'address_id' => isset($row['address_id'])? $row['address_id']:null,
                    'created_by' => isset($row['created_by'])? $row['created_by']:$user_id,
                 ]);
            }
            else
            {
                $order->increment('total_qty', $row['quantity']);
                $order->increment('shipped_qty', $row['quantity']);
                $order->increment('sub_total', $row['amount']);
                $order->increment('grand_total', $row['amount']);
            }
            OrderDetails::insert([
                
                'active' => 'Y',
                'order_id' => isset($order['id'])? $order['id']:null,
                'product_id' => isset($row['product_id'])? $row['product_id']:null,
                'quantity' => isset($row['quantity'])? $row['quantity']:0,
                'shipped_qty' => isset($row['shipped_qty'])? $row['shipped_qty']:0,
                'price' => isset($row['price'])? $row['price']:0.00,
                'discount' => isset($row['discount'])? $row['discount']:0.00,
                'discount_amount' => isset($row['discount_amount'])? $row['discount_amount']:0.00,
                'tax_amount' => isset($row['tax_amount'])? $row['tax_amount']:0.00,
                'line_total' => isset($row['amount'])? $row['amount']:0.00,
                'status_id' => isset($row['status_id'])? $row['status_id']:null,
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|regex:/[a-zA-Z0-9\s]+/',
        ];
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function onFailure(Failure ...$failures)
    {
        Log::stack(['import-failure-logs'])->info(json_encode($failures));
    }
}
