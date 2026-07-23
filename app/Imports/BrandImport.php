<?php

namespace App\Imports;

use App\Models\Brand;
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
class BrandImport implements ToModel,WithValidation,WithHeadingRow, WithBatchInserts , WithChunkReading
{
    use Importable, SkipsFailures;

    public function model(array $row)
    {
        return new Brand([
            'active' => 'Y',
            'brand_name' => isset($row['brand_name'])? ucfirst($row['brand_name']):'',
            'brand_image' => isset($row['brand_image'])? $row['brand_image']:'',
            'created_by' => Auth::user()->id,
            'created_at' => getcurentDateTime() ,
            'updated_at' => getcurentDateTime()
        ]);
    }

    public function rules(): array
    {
        return [
            'brand_name' => 'required|string|regex:/[a-zA-Z0-9\s]+/',
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
