<?php

namespace App\Imports;

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
use App\Models\SalesTargetUsers;
use App\Models\User;
use Validator;

class SalesTargetUsersImport implements ToCollection, WithValidation, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    use Importable;

    public function model(array $row)
    {
        return new SalesTargetUsers([
            //
        ]);
    }
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if (!isset($row['user_id']) || empty($row['user_id'])) {
                continue;
            }
            $user_id = $row['user_id'];
            $type = $row['type'];
            $branchId = $row['branch_id'];
            foreach ($row as $key => $value) {
                $key = (string) $key;
                if (preg_match('/^(\d{2})(\d{2})$/', $key, $matches)) {
                    $monthNumber = $matches[1];
                    $year = '20' . $matches[2];
                    $carbonDate = Carbon::createFromFormat('Y-m-d', "$year-$monthNumber-01");
                    $month = $carbonDate->format('M');
                } elseif (is_numeric($key) && $key > 40000) {
                    $excelDate = $key - 25569;
                    $carbonDate = Carbon::createFromTimestamp($excelDate * 86400);
                    $month = $carbonDate->format('M');
                    $year = $carbonDate->format('Y');
                } else {
                    continue;
                }
                $targetValue = is_numeric($value) ? $value : 0;
                $salesTargetUsers = SalesTargetUsers::updateOrCreate([
                    'user_id' => $user_id,
                    'month' => $month,
                    'year' => $year,
                    'branch_id' => $branchId
                ], [
                    'type' => $type,
                    'target' => $targetValue
                ]);
            }
        }
    }
    public function rules(): array
    {
        $rules = [
            'user_id' => 'required',
            'type' => 'required|in:primary,secondary',
        ];
        return $rules;
    }

    public function customValidationMessages()
    {
        return [
            'user_id.required' => 'The user id is required.',
            'type.required' => 'The type name field is required.',
            'type.in' => 'The type name field either have primary or secondary value.',
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
