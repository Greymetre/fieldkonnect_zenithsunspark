<?php

namespace App\Imports;

use App\Models\BranchOprningQuantity;
use App\Models\Product;
use App\Models\WareHouse;
use App\Models\OpeningStock;
use Carbon\Carbon;
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
use PhpOffice\PhpSpreadsheet\Shared\Date;

class OpeningQuantityImport implements ToCollection,WithValidation,WithHeadingRow, WithBatchInserts , WithChunkReading
{
    use Importable, SkipsFailures;
    protected $file;

    public function __construct($file = null)
    {
        $this->file = $file;
        // dd('File received in constructor', $file); // REMOVE THIS to allow collection() to run
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if (is_numeric($row['qty_month'])) {
                $row['qty_month'] = Date::excelToDateTimeObject($row['qty_month'])->format('Y-m-d');
            }
            $row['qty_month'] = Carbon::createFromFormat('M-y', $row['qty_month'])->firstOfMonth()->format('Y-m-d');
            
            $branch_ids = explode(',', $row['branch_id']);


            foreach ($branch_ids as $branch_id) {
                $openingStock = BranchOprningQuantity::where('item_code', $row['itm_code'])
                    ->where('item_description', $row['itm_desc'])
                    ->where('item_group', $row['itm_grp_name'])
                    ->where('qty_month', $row['qty_month'])
                    ->whereRaw("FIND_IN_SET(?, branch_id)", [$branch_id])
                    ->first();

                $data = [
                    'item_code' => $row['itm_code'] ?? null,
                    'branch_id' => $row['branch_id'],
                    'item_description' => $row['itm_desc'] ?? null,
                    'item_group' => $row['itm_grp_name'] ?? null,
                    'qty_month' => $row['qty_month'] ?? null,
                    'open_order_qty' => $row['opening_qty'] ?? 0,
                ];

                if ($openingStock) {
                    if($row['opening_qty'] == 0){
                       $openingStock->delete();
                    }else{
                      $openingStock->update($data);
                    }
                } else {
                    if(!$row['opening_qty'] == 0){
                      BranchOprningQuantity::create($data);
                    }
                }
            }
                // if (isset($row['itm_code']) && isset($branch_id)) {
                //    $stock =OpeningStock::updateOrCreate(
                //         [
                //             'item_code' => $row['itm_code'],
                //             'branch_id'  => $branch_id,
                //             'item_description'       => $row['itm_desc'] ?? null,
                //             'item_group'       => $row['itm_grp_name'] ?? null,
                //         ],
                //         [
                //             'ware_house_name' => $row['warehouse_name'] ?? null,  // Use ternary instead of isset()
                //             'opening_stocks' => $row['instock_qty'] ?? 0,             // Use null coalescing operator
                //             'open_order_qty'    => $row['opening_qty'] ?? 0
                //         ]
                //     );
                // }
            // }

        }
    }

    public function rules(): array
    {
        return [
            'itm_code' => 'required|string',
            'itm_desc' => 'nullable|string',
            'itm_grp_name' => 'nullable|string',
            'branch_id' => ['nullable', 'regex:/^\d+(,\d+)*$/'],
            'opening_qty' => 'nullable|numeric',
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
