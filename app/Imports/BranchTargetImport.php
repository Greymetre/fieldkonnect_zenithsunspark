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
use App\Models\BranchWiseTarget;
use App\Models\User;
use Validator;

class BranchTargetImport implements ToCollection,WithValidation,WithHeadingRow, WithBatchInserts , WithChunkReading
{
    use Importable;
    
    public function model(array $row)
    {
        return new BranchWiseTarget([
            //
        ]);
    }
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $excelDate = $row['month'] - 25569; // Adjust for Excel's epoch
            $unixTimestamp = strtotime('+'.$excelDate.' days', strtotime('1970-01-01'));
            $carbonDate = Carbon::createFromTimestamp($unixTimestamp);
            $carbonMonth = $carbonDate->format('M');
            $carbonYear = $carbonDate->format('Y');
            // dd($carbonDate->format('Y'));

            // $salesTargetUsers = BranchWiseTarget::updateOrCreate([
            //     'user_id' => $row['user_id'],
            //     'month' => $carbonMonth,
            //     'year' => $carbonYear
            //     ],[

            //     'user_id' => $row['user_id'],
            //     'user_name' => $row['user_name'],
            //     'div_id' => $row['div_id'],
            //     'division_name' => $row['div_name'],
            //     'branch_id' => $row['branch_id'],
            //     'branch_name' => $row['branch_name'],
            //     'type' => $row['type'],
            //     'month' => $carbonMonth,
            //     'year' => $carbonYear,
            //     'target' => $row['target_value']
            // ]);


            $salesTargetUsers = BranchWiseTarget::create([
                'user_id' => $row['user_id'],
                'user_name' => $row['user_name'],
                'div_id' => $row['div_id'],
                'division_name' => $row['div_name'],
                'branch_id' => $row['branch_id'],
                'branch_name' => $row['branch_name'],
                'type' => $row['type'],
                'month' => $carbonMonth,
                'year' => $carbonYear,
                'target' => $row['target_value']
            ]);
        }
    }
    public function rules(): array
    {
        $rules = [
            'user_id' => 'required',
            'month' => 'required',
            'type' => 'required|in:primary,secondary',
            'target_value' => 'required|numeric',
        ];
        return $rules;
    }

    public function customValidationMessages()
    {
        return [
            'user_id.required' => 'The user id is required.',
            'month.required' => 'The month name field is required.',
            'type.required' => 'The type name field is required.',
            'type.in' => 'The type name field either have primary or secondary value.',
            'target_value.required' => 'The target value is required.',
            'target_value.required' => 'The target value must be numeric.'
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
