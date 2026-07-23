<?php

namespace App\Imports;

use App\Models\UserCityAssign;
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


class UserCityImport implements ToCollection,WithValidation,WithHeadingRow, WithBatchInserts , WithChunkReading
{
    use Importable, SkipsFailures;

    

    public function model(array $row)
    {
        // return new City([
        //     'active' => 'Y',
        //     'city_name' => isset($row['city_name'])? ucfirst($row['city_name']):'',
        //     'district_id' => isset($row['district_id'])? $row['district_id']:null,
        //     'created_at' => getcurentDateTime() ,
        //     'updated_at' => getcurentDateTime()
        // ]);
    }
    public function collection(Collection $rows)
    {
        
        foreach ($rows as $row) {
            
            if(isset($row['delete']) && $row['delete'] == 'Y'){
                UserCityAssign::where('city_id', $row['city_id'])->where('userid', $row['user_id'])->delete();
            }else{
                UserCityAssign::updateOrCreate(['city_id' => $row['city_id'], 'userid' => $row['user_id'] ],[
                    'userid' => $row['user_id'],
                    'reportingid' => $row['reportingid'],
                    'city_id' => $row['city_id'],
                    'created_at' => getcurentDateTime() ,
                    'updated_at' => getcurentDateTime()
                ]);
            }
        }
    }

    public function rules(): array
    {
        return [
            // 'city_name' => 'required|string|regex:/[a-zA-Z0-9\s]+/',
            // 'district_id' => 'required|exists:districts,id',
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

}
