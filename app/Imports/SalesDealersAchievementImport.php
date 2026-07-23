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

class SalesDealersAchievementImport implements ToCollection,WithValidation,WithHeadingRow, WithBatchInserts , WithChunkReading
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

        // dd($rows);
        foreach ($rows as $row) {
            $excelDate = $row['month'] - 25569; // Adjust for Excel's epoch
            $unixTimestamp = strtotime('+'.$excelDate.' days', strtotime('1970-01-01'));
            $carbonDate = Carbon::createFromTimestamp($unixTimestamp);
            $carbonMonth = $carbonDate->format('M');
            $carbonYear = $carbonDate->format('Y');

            $salesTargetCustomers = SalesTargetCustomers::updateOrCreate([
                'customer_id' => $row['customer_id'],
                'month' => $carbonMonth,
                'year' => $carbonYear],[
                    'customer_id' => $row['customer_id'],
                    'type' => $row['type'],
                    'month' => $carbonMonth,
                    'year' => $carbonYear,
                    'achievement' => $row['achievement']                
            ]);
        }
    }

    public function rules(): array
    {
        $rules = [
            'customer_id' => 'required',
            'month' => 'required',
            'type' => 'required|in:primary,secondary',
            'achievement' => 'required|numeric',
        ];
        return $rules;
    }

    public function customValidationMessages()
    {
        return [
            'customer_id.required' => 'The customer id id is required.',
            'month.required' => 'The month name field is required.',
            'type.in' => 'The type name field either have primary or secondary value.',
            'target_value.required' => 'The target value is required.',
            'achievement.required' => 'The achievement value is required.',
            'achievement.numeric' => 'The achievement value must be numeric.'
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
