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
use App\Models\SalesTargetCustomers;
use App\Models\User;
use Validator;

class SalesTargetDealersImport implements ToCollection, WithValidation, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    use Importable;

    public function model(array $row)
    {
        return new SalesTargetCustomers([
            //
        ]);
    }
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Skip invalid rows
            if (!isset($row['customer_id']) || empty($row['customer_id'])) {
                continue;
            }

            // Extract static fields
            $customerId = $row['customer_id'];
            $type = $row['type'];
            $branchId = $row['branch_id'];
            $divId = $row['div_id'];
            $dealer = $row['dealer'] ?? null;

            foreach ($row as $key => $value) {
                $key = (string) $key; // Ensure it's always a string

                // Case 1: MMYY format (e.g., "0424", "0125")
                if (preg_match('/^(\d{2})(\d{2})$/', $key, $matches)) {
                    $monthNumber = $matches[1]; // Extract MM
                    $year = '20' . $matches[2]; // Convert YY to YYYY

                    // Convert to readable format
                    $carbonDate = Carbon::createFromFormat('m Y', $monthNumber . ' ' . $year);
                    $month = $carbonDate->format('M');
                }
                // Case 2: Excel numeric date format (e.g., 45383, 45413)
                elseif (is_numeric($key) && $key > 40000) { // Excel dates start around 40000
                    $excelDate = $key - 25569; // Adjust for Excel's epoch
                    $carbonDate = Carbon::createFromTimestamp($excelDate * 86400); // Convert to timestamp
                    $month = $carbonDate->format('M');
                    $year = $carbonDate->format('Y');
                }
                // Skip if not a valid date key
                else {
                    continue;
                }

                // Ensure target value is numeric
                $targetValue = is_numeric($value) ? $value : 0;

                // Save data in the database
                SalesTargetCustomers::updateOrCreate([
                    'customer_id' => $customerId,
                    'month' => $month,
                    'year' => $year
                ], [
                    'customer_id' => $customerId,
                    'type' => $type,
                    'branch_id' => $branchId,
                    'div_id' => $divId,
                    'dealer' => $dealer,
                    'month' => $month, // "Apr", "May", etc.
                    'year' => $year,
                    'target' => $targetValue
                ]);
            }
        }
    }
    public function rules(): array
    {
        $rules = [
            'customer_id' => 'required|exists:customers,id',
            'type' => 'required|in:primary,secondary'
        ];
        return $rules;
    }

    public function customValidationMessages()
    {
        return [
            'customer_id.required' => 'The customer id is required.',
            'customer_id.exists' => 'The customer id does not exists.',
            'type.required' => 'The type name field is required.',
            'type.in' => 'The type name field either have primary or secondary value.',
            'target_value.required' => 'The target value is required.',
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
