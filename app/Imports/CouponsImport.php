<?php

namespace App\Imports;

use App\Models\Coupons;
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

class CouponsImport implements ToModel,WithValidation,WithHeadingRow, WithBatchInserts , WithChunkReading
{
    use Importable, SkipsFailures;

    public function model(array $row)
    {
        return new Coupons([
            'active' => 'Y',
            'coupon_code' => isset($row['coupon_code'])? ucfirst($row['coupon_code']):'',
            'expiry_date' => isset($row['expiry_date'])? $row['expiry_date']:'',
            'generated_date' => isset($row['generated_date'])? $row['generated_date']:'',
            'customer_code' => isset($row['customer_code'])? $row['customer_code']:'',
            'invoice_date' => isset($row['invoice_date'])? $row['invoice_date']:'',
            'invoice_no' => isset($row['invoice_no'])? $row['invoice_no']:'',
            'product_code' => isset($row['product_code'])? $row['product_code']:'',
            'status_id' => isset($row['status_id'])? $row['status_id']:null,
            'product_id' => isset($row['product_id'])? $row['product_id']:null,
            'coupon_profile_id' => isset($row['coupon_profile_id'])? $row['coupon_profile_id']:null,
            'created_at' => getcurentDateTime() ,
            'updated_at' => getcurentDateTime()
        ]);
    }

    public function rules(): array
    {
        return [
            'coupon_code' => 'required|string|regex:/[a-zA-Z0-9\s]+/',
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
