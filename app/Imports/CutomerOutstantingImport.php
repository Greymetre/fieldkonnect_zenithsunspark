<?php

namespace App\Imports;

use App\Models\CustomerOutstanting;
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

class CutomerOutstantingImport implements ToCollection, WithValidation, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    use Importable;

    public function model(array $row)
    {
        return new CustomerOutstanting([
            //
        ]);
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {

            foreach ($row as $k => $val) {
                if($k == '0_30' || $k == '31_60' || $k == '61_90' || $k == '91_150' || $k == '150'){
                    $k = str_replace('_','-',$k);
                    $salesTargetUsers = CustomerOutstanting::updateOrCreate(
                        [
                            'customer_id' => $row['customer_id'],
                            'days' => (string)$k,
                            'year' => $row['year'],
                            'division_id' => $row['division_id'],
                            'branch_id' => $row['branch_id'],
                            'quarter' => $row['quarter']
                        ],
                        [
                            'user_id' => $row['user_id'],
                            'customer_name' => $row['customer_name'],
                            'amount' => $val
                        ]
                    );
                }
            }
        }
    }

    public function rules(): array
    {
        $rules = [
            'customer_id' => 'required|exists:customers,id',
            'user_id' => 'nullable|exists:users,id',
            'branch_id' => 'required|exists:branches,id',
            'division_id' => 'required|exists:divisions,id',
        ];
        return $rules;
    }

    public function customValidationMessages()
    {
        return [
            'customer_id.required' => 'The Customer ID is required.',
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
