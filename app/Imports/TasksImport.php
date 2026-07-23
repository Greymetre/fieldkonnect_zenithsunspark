<?php

namespace App\Imports;

use App\Models\Tasks;
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

class TasksImport implements ToModel,WithValidation,WithHeadingRow, WithBatchInserts , WithChunkReading
{
    use Importable, SkipsFailures;

    
    
    public function model(array $row)
    {
        return new Tasks([
            'active' => 'Y',
            'user_id' => isset($row['user_id'])? $row['user_id']:null,
            'title' => isset($row['title'])? $row['title']:'',
            'descriptions' => isset($row['descriptions'])? $row['descriptions']:'',
            'hourly_rate' => isset($row['hourly_rate'])? $row['hourly_rate']:0.00,
            'total_amount' => isset($row['total_amount'])? $row['total_amount']:0.00,
            'start_date' => isset($row['start_date'])? $row['start_date']:null,
            'due_date' => isset($row['due_date'])? $row['due_date']:null,
            'priority' => isset($row['priority'])? $row['priority']:'',
            'repeat_every' => isset($row['repeat_every'])? $row['repeat_every']:'',
            'relatedto' => isset($row['relatedto'])? $row['relatedto']:'',
            'estimate_id' => isset($row['estimate_id'])? $row['estimate_id']:null,
            'proposal_id' => isset($row['proposal_id'])? $row['proposal_id']:null,
            'customer_id' => isset($row['customer_id'])? $row['customer_id']:null,
            'status_id' => isset($row['status_id'])? $row['status_id']:null,
            'created_by' => Auth::user()->id,
            'created_at' => getcurentDateTime(),
            'updated_at' => getcurentDateTime()
        ]);
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|regex:/[a-zA-Z0-9\s]+/',
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
