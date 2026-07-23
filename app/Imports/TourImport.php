<?php

namespace App\Imports;

use App\Models\TourProgramme;
use App\Models\TourDetail;
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

use App\Models\City;

class TourImport implements ToCollection,WithValidation,WithHeadingRow, WithBatchInserts , WithChunkReading
{
    use Importable, SkipsFailures;

    

    public function model(array $row)
    {
        return new TourProgramme([
            //
        ]);
    }

    public function collection(Collection $rows)
    {
        $details = collect([]);
        foreach ($rows as $row) {
            if( $tour = TourProgramme::create([
                'date' => isset($row['date']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['date'])->format('Y-m-d') : null,
                'userid' => isset($row['userid']) ? $row['userid'] : null,
                'town' => isset($row['town']) ? ucfirst(strtolower($row['town'])):'',
                'objectives' => isset($row['objectives']) ? $row['objectives']:'',
                'created_at' => getcurentDateTime() ,
                'updated_at' => getcurentDateTime()
            ]) )
            {
                $towns = explode(',', preg_replace('/\s*,\s*/', ',', $row['town']));
                foreach ($towns as $key => $town) {
                    $cityid = City::where('city_name','=',$town)->pluck('id')->first();
                    $visited = TourDetail::whereHas('tourinfo',function($query) use($row){
                                            $query->where('userid','=',$row['userid']);
                                        })
                                        ->where('visited_cityid','=',$cityid)
                                        ->whereNotNull('visited_date')
                                        ->select('visited_date')
                                        ->latest()
                                        ->first();  
                    $lastvisited = (isset($cityid) && !empty($visited)) ? $visited['visited_date'] : null;
                    TourDetail::create([
                        'tourid' => $tour['id'],
                        'city_id' => isset($cityid)? $cityid :null,
                        'last_visited' => $lastvisited,
                        'created_at' => getcurentDateTime(),
                        'updated_at' => getcurentDateTime()
                    ]); 
                }
            }
        }
    }

    public function rules(): array
    {
        return [
            'town' => 'required|string',
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
