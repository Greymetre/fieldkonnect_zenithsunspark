<?php

namespace App\Imports;

use App\Models\TransactionHistory;
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
class ManualTransactionImport implements ToModel,WithValidation,WithHeadingRow, WithBatchInserts , WithChunkReading
{
    use Importable, SkipsFailures;

    public function model(array $row)
    {
        return new TransactionHistory([
            'customer_id' => isset($row['customer_id'])? ucfirst($row['customer_id']):'',
            'status' => isset($row['point_type'])? $row['point_type']:'',
            'point' => isset($row['points'])? $row['points']:'',
            'remark' => isset($row['remark'])? $row['remark']:NULL,
            'created_by' => Auth::user()->id,
            'created_at' => getcurentDateTime(),
            'updated_at' => getcurentDateTime()
        ]);
    }

    public function rules(): array
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'point_type' => 'required',
            'points' => 'required',
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
