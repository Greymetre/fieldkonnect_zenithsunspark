<?php

namespace App\Imports;

use App\Models\City;
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
use App\Models\User;
use App\Models\State;
use App\Models\UserCityAssign;
use App\Models\District;
class CityImport implements ToCollection,WithValidation,WithHeadingRow, WithBatchInserts , WithChunkReading
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
    // public function collection(Collection $rows)
    // {

    //     foreach ($rows as $row) {
    //         UserCityAssign::updateOrCreate(['city_id' => $row['cityid'], 'userid' => $row['userid'] ],[
    //             'userid' => $row['userid'],
    //             'reportingid' => $row['reportingid'],
    //             'city_id' => $row['cityid'],
    //             'created_at' => getcurentDateTime() ,
    //             'updated_at' => getcurentDateTime()
    //         ]);
    //     }
    // }
    public function collection(Collection $rows)
    {

        $userdetails = collect([]);
        foreach ($rows as $row) {
            $state_id = District::where('id','=',$row['district_id'])->pluck('state_id')->first();
            if( $city = City::updateOrCreate(['id' => $row['id']],[
                'active' => 'Y',
                'city_name' => isset($row['city_name'])? $row['city_name']:'',
                'district_id' => isset($row['district_id'])? $row['district_id']:null,
                'grade' => isset($row['grade'])? $row['grade']:null,
                'state_id' => $state_id,
                'created_at' => getcurentDateTime() ,
                'updated_at' => getcurentDateTime()
            ]) )
            {
                if(!empty($row['userid']))
                {
                    $userids = explode(',', $row['userid']);
                    foreach ($userids as $key => $value) {
                        UserCityAssign::updateOrCreate(['city_id' => $city['id'],'userid' => $value],[
                            'userid' => $value,
                            'reportingid' => isset($row['reportingid'])? $row['reportingid']:null,
                            'city_id' => $city['id'],
                            'created_at' => getcurentDateTime() ,
                            'updated_at' => getcurentDateTime()
                        ]);
                       
                    }
                }
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

    public function onFailure(Failure ...$failures)
    {
        Log::stack(['import-failure-logs'])->info(json_encode($failures));
    }
}
