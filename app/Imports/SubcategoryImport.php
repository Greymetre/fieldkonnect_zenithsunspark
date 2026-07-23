<?php

namespace App\Imports;

use App\Models\Subcategory;
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

class SubcategoryImport implements ToCollection, WithValidation, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    use Importable, SkipsFailures;



    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // dd($row);
            Subcategory::updateOrCreate(
                [
                    'subcategory_name' => isset($row['subcategory_name']) ? $row['subcategory_name'] : '',
                    'category_id' => isset($row['category_id']) ? $row['category_id'] : NULL,

                ],
                [
                    'active' => 'Y',
                    'sap_code' => isset($row['sap_code']) ? $row['sap_code'] : NULL,
                    'service_category_id' => isset($row['service_category_id']) ? $row['service_category_id'] : NULL,
                    'updated_by' => Auth::user()->id,
                    'updated_at' => getcurentDateTime()
                ]
            );
        }
    }

    public function rules(): array
    {
        return [
            'subcategory_name' => 'required',
            'category_id' => 'nullable|exists:categories,id',
            'service_category_id' => [
                'nullable',
                'regex:/^\d+(,\d+)*$/',
                function ($attribute, $value, $fail) {
                    $ids = explode(',', $value);
                    $count = DB::table('service_charge_categories')->whereIn('id', $ids)->count();
                    if ($count !== count($ids)) {
                        $fail("One or more selected service categories do not exist.");
                    }
                },
            ],
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
