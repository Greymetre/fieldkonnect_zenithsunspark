<?php

namespace App\Imports;

use App\Models\Scheme;
use App\Models\SchemeDetails;
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
use Illuminate\Validation\Rule;
use Log;

use Illuminate\Support\Facades\Auth;

class SchemeImport implements ToCollection,WithValidation,WithHeadingRow, WithBatchInserts , WithChunkReading
{
    use Importable, SkipsFailures;

    private $scheme_id;
    
    
    public function __construct($id)
    {
        $this->scheme_id = $id;
    }
    
    public function model(array $row)
    {
        return new Scheme([
            //
        ]);
    }
    public function collection(Collection $rows)
    {
        
        foreach ($rows as $row) {
            $scheme = SchemeDetails::updateOrCreate([
                'scheme_id' => decrypt($this->scheme_id),
                'product_id' => isset($row['product_id'])? $row['product_id']:null,
            ],[
                
                'active' => 'Y',
                'scheme_id' => decrypt($this->scheme_id),
                'product_id' => isset($row['product_id'])? $row['product_id']:null,
                'category_id' => isset($row['category_id'])? $row['category_id']:null,
                'subcategory_id' => isset($row['sub_category_id'])? $row['sub_category_id']:null,
                'active_point' => isset($row['active_point'])? $row['active_point']:"0",
                'provision_point' => isset($row['provision_point'])? $row['provision_point']:"0",
                'points' => isset($row['points'])? $row['points']:"0"
            ]);
        }
    }
    public function rules(): array
    {
        return [
            'product_id' => [
                'required',
                Rule::exists('products', 'id')
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
