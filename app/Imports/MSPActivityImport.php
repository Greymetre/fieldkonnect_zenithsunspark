<?php

namespace App\Imports;

use App\Models\BranchStock;
use App\Models\CustomerOutstanting;
use App\Models\MspActivity;
use App\Models\Product;
use App\Models\ProductDetails;
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
use App\Models\Services;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\PrimarySales;
use App\Models\User;
use Validator;

class MSPActivityImport implements ToCollection, WithValidation, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    use Importable;

    public function model(array $row)
    {
        return new BranchStock([
            //
        ]);
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            MspActivity::updateOrCreate([
                'emp_code' => $row['emp_code'],
                'fyear' => $row['fyear'],
                'month' => $row['month'],
            ],[
                'msp_count' => $row['msp_count'],
            ]);
        }
    }

    public function rules(): array
    {
        $rules = [
            'emp_code' => 'required|exists:users,employee_codes',
        ];
        return $rules;
    }

    // public function customValidationMessages()
    // {
    //     return [
    //         'emp_code.required' => 'The Branch ID is required.',
    //     ];
    // }

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
