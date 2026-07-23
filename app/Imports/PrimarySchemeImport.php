<?php

namespace App\Imports;

use App\Models\OrderScheme;
use App\Models\OrderSchemeDetail;
use App\Models\PrimaryScheme;
use App\Models\PrimarySchemeDetail;
use App\Models\Product;
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

class PrimarySchemeImport implements ToCollection,WithValidation,WithHeadingRow, WithBatchInserts , WithChunkReading
{
    use Importable, SkipsFailures;

    private $scheme_id;
    
    
    public function __construct($id)
    {  
        $this->scheme_id = $id;
    }
    
    public function model(array $row)
    {
        return new PrimaryScheme([
            //
        ]);
    }
    public function collection(Collection $rows)
    {
        foreach($rows as $row) {
            if(isset($row['product_sap_code']) && !empty($row['product_sap_code'])){
                $product = Product::where('sap_code', $row['product_sap_code'])->first();
                if($product){
                    $scheme = PrimarySchemeDetail::updateOrCreate([
                        'primary_scheme_id' => decrypt($this->scheme_id),
                        'product_id' => $product->id,
                        'sap_code' => $product->sap_code,
                        'category_id' => isset($row['category_id'])? $row['category_id']:null,
                        'subcategory_id' => isset($row['sub_category_id'])? $row['sub_category_id']:null,
                    ],[
                        'active' => 'Y',
                        'points' => isset($row['point'])? $row['point']:null
                    ]);
                }

            }else{
                $scheme = PrimarySchemeDetail::updateOrCreate([
                    'primary_scheme_id' => decrypt($this->scheme_id),
                    'group_type' => !empty($row['group_type']) ? $row['group_type'] : null,
                    'groups' => isset($row['group_name'])? $row['group_name']:null,
                    'min' => isset($row['min'])? $row['min']:null,
                    'max' => isset($row['max'])? $row['max']:null,
                ],[
                    'active' => 'Y',
                    'points' => isset($row['points'])? $row['points']:null
                ]);
            }
        }
    }
    public function rules(): array
    {
        return [
            // 'group_type' => [
            //     'required',
            // ],
            // 'group_name' => [
            //     'required',
            //     Rule::exists('primary_sales', 'new_group_name')
            // ],
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
