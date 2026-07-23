<?php

namespace App\Imports;

use App\Models\Beat;
use App\Models\BeatCustomer;
use App\Models\BeatUser;
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
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
class BeatImport implements ToCollection,WithValidation,WithHeadingRow, WithBatchInserts , WithChunkReading
{
    use Importable, SkipsFailures;

    public function model(array $row)
    {
        return new Beat([
            //
        ]);
    }

    public function collection(Collection $rows)
    {
        $beatcustomers = collect([]);
        $beatusers = collect([]);
        foreach ($rows as $row) {
            if( $beat = Beat::create([
                'active' => 'Y',
                'beat_name' => isset($row['beat_name'])? ucfirst($row['beat_name']):'',
                'description' => isset($row['description'])? $row['description']:'',
                'region_id' => isset($row['region_id'])? $row['region_id']:null,
                'country_id' => isset($row['country_id'])? $row['country_id']:null,
                'state_id' => isset($row['state_id'])? $row['state_id']:null,
                'district_id' => isset($row['district_id'])? $row['district_id']:null,
                'city_id' => isset($row['city_id'])? $row['city_id']:null,
                'created_by' => Auth::user()->id,
                'created_at' => getcurentDateTime() ,
                'updated_at' => getcurentDateTime()
            ]) )
            {
                if(!empty($row['customers']))
                {
                    $customers = explode(',', preg_replace('/\s*,\s*/', ',', $row['customers'])); 
                    foreach ($customers as $key => $customer) {
                        BeatCustomer::updateOrCreate(['customer_id' => $customer],[
                            'active' => 'Y',
                            'beat_id' => $beat['id'],
                            'customer_id' => $customer,
                            'created_at' => getcurentDateTime() ,
                            'updated_at' => getcurentDateTime()
                        ]);
                    }
                }
                if(!empty($row['userid']))
                {
                    $users = explode(',', preg_replace('/\s*,\s*/', ',', $row['userid']));
                    foreach ($users as $key => $user) {
                        BeatUser::updateOrCreate(['beat_id' => $beat['id'], 'user_id' => $user],[
                            'beat_id' => $beat['id'],
                            'user_id' => $user,
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
            'beat_name' => 'required|string|regex:/[a-zA-Z0-9\s]+/',
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
