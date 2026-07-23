<?php

namespace App\Imports;

use App\Models\Pincode;
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

class PincodeImport implements ToModel,WithValidation,WithHeadingRow, WithBatchInserts , WithChunkReading
{
    use Importable, SkipsFailures;

    
    
    public function model(array $row)
    {
        return new Pincode([
            'active' => 'Y',
            'pincode' => isset($row['pincode'])? $row['pincode']:'',
            'city_id' => isset($row['city_id'])? $row['city_id']:null,
            'created_by' => Auth::user()->id,
            'created_at' => getcurentDateTime() ,
            'updated_at' => getcurentDateTime()
        ]);
    }
    public function rules(): array
    {
        return [
            'pincode' => 'required',
            'city_id' => 'required|exists:cities,id',
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
