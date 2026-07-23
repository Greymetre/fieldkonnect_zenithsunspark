<?php

namespace App\Imports;

use App\Models\Support;
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

class SupportImport implements ToModel,WithValidation,WithHeadingRow, WithBatchInserts , WithChunkReading
{
    use Importable, SkipsFailures;
    
    public function model(array $row)
    {
        return new Support([
            'active' => 'Y',
            'subject' => isset($row['subject'])? $row['subject']:'',
            'description' => isset($row['description'])? $row['description']:'',
            'department_id' => isset($row['department_id'])? $row['department_id']:null,
            'user_id' => isset($row['user_id'])? $row['user_id']:null,
            'status_id' => isset($row['status_id'])? $row['status_id']:null,
            'customer_id' => isset($row['customer_id'])? $row['customer_id']:null,
            'name' => isset($row['name'])? $row['name']:'',
            'mobile' => isset($row['mobile'])? $row['mobile']:'',
            'email' => isset($row['email'])? $row['email']:'',
            'priority' => isset($row['priority'])? $row['priority']:'Low',
            'last_reply' => isset($row['last_reply'])? $row['last_reply']:getcurentDateTime(),
            'created_at' => getcurentDateTime(),
            'updated_at' => getcurentDateTime()
        ]);
    }

    public function rules(): array
    {
        return [
            'subject' => 'required|string|regex:/[a-zA-Z0-9\s]+/',
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
