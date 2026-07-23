<?php

namespace App\Imports;

use App\Models\ServiceChargeProducts;
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

class ServiceProductImport implements ToCollection, WithValidation, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    use Importable, SkipsFailures;


    public function model(array $row)
    {
        return new ServiceChargeProducts([]);
    }

    public function collection(Collection $rows)
    {

        foreach ($rows as $row) {
            
            if (isset($row['id']) && !empty($row['id'])) {
                $ServiceChargeProducts = ServiceChargeProducts::find($row['id']);
                $ServiceChargeProducts->active = 'Y';
                $ServiceChargeProducts->charge_type_id = isset($row['charge_type_id']) ? $row['charge_type_id'] : '';
                $ServiceChargeProducts->product_name = isset($row['product_name']) ? $row['product_name'] : null;
                $ServiceChargeProducts->division_id = isset($row['division_id']) ? $row['division_id'] : null;
                $ServiceChargeProducts->category_id = isset($row['category_id']) ? $row['category_id'] : null;
                $ServiceChargeProducts->price = isset($row['price']) ? $row['price'] : null;
                $ServiceChargeProducts->other_charge = isset($row['other_charge']) ? $row['other_charge'] : null;
                $ServiceChargeProducts->created_by = Auth::user()->id;
                $ServiceChargeProducts->updated_at = getcurentDateTime();
                $ServiceChargeProducts->save();
            } else {
                ServiceChargeProducts::create([
                    'active' => 'Y',
                    'charge_type_id' => isset($row['charge_type_id']) ? $row['charge_type_id'] : '',
                    'product_name' => isset($row['product_name']) ? $row['product_name'] : '',
                    'division_id' => isset($row['division_id']) ? $row['division_id'] : null,
                    'category_id' => isset($row['category_id']) ? $row['category_id'] : null,
                    'price' => isset($row['price']) ? $row['price'] : null,
                    'other_charge' => isset($row['other_charge']) ? $row['other_charge'] : null,
                    'created_by' => Auth::user()->id,
                    'created_at' => getcurentDateTime(),
                    'updated_at' => getcurentDateTime()
                ]);
            }
        }
    }

    public function rules(): array
    {
        return [
            'charge_type_id' => 'required',
            'product_name' => 'required',
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
