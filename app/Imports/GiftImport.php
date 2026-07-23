<?php

namespace App\Imports;

use App\Models\Gifts;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class GiftImport implements ToCollection,WithValidation,WithHeadingRow, WithBatchInserts , WithChunkReading
{
    use Importable, SkipsFailures;


    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if(isset($row['id'])){
                $gift = Gifts::where('id','=',$row['id'])->first();
            }else{
                $gift = null;
            }
            if ($gift === null) {
                $order = Gifts::create([
                    
                    'active' => 'Y',
                    'product_name' => isset($row['product_name'])? $row['product_name']:null,
                    'display_name' => isset($row['display_name'])? $row['display_name']:null,
                    'description' => isset($row['description'])? $row['description']:'',
                    'mrp' => isset($row['mrp'])? $row['mrp']:0,
                    'price' => isset($row['price'])? $row['price']:0,
                    'points' => isset($row['points'])? $row['points']:0,
                    'subcategory_id' => isset($row['subcategory_id'])? $row['subcategory_id']:null,
                    'category_id' => isset($row['category_id'])? $row['category_id']:null,
                    'brand_id' => isset($row['brand_id'])? $row['brand_id']:null,
                    'unit_id' => isset($row['model_id'])? $row['model_id']:null,
                    'customer_type_id' => isset($row['customer_type_id'])? $row['customer_type_id']:null,
                    'created_by' => auth()->user()->id,
                 ]);
            }
            else
            {
                $gift->product_name = isset($row['product_name'])? $row['product_name']:null;
                $gift->display_name = isset($row['display_name'])? $row['display_name']:null;
                $gift->description = isset($row['description'])? $row['description']:null;
                $gift->mrp = isset($row['mrp'])? $row['mrp']:0;
                $gift->price = isset($row['price'])? $row['price']:0;
                $gift->points = isset($row['points'])? $row['points']:0;
                $gift->subcategory_id = isset($row['subcategory_id'])? $row['subcategory_id']:null;
                $gift->category_id = isset($row['category_id'])? $row['category_id']:null;
                $gift->brand_id = isset($row['brand_id'])? $row['brand_id']:null;
                $gift->unit_id = isset($row['model_id'])? $row['model_id']:null;
                $gift->customer_type_id = isset($row['customer_type_id'])? $row['customer_type_id']:null;
                $gift->created_by = auth()->user()->id;
                $gift->save();
                
            }
        }
    }

    public function rules(): array
    {
        return [
            'product_name' => 'required|string|regex:/[a-zA-Z0-9\s]+/',
            'customer_type_id' => ['required',Rule::exists('customer_types', 'id')],
            'model_id' => ['required',Rule::exists('gift_models', 'id')],
            'brand_id' => ['required',Rule::exists('gift_brands', 'id')],
            'category_id' => ['required',Rule::exists('gift_categories', 'id')],
            'subcategory_id' => ['required',Rule::exists('giftsubcategories', 'id')],
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
