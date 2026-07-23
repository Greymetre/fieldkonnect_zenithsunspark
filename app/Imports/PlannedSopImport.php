<?php

namespace App\Imports;

use App\Models\PlannedSOP;
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

class PlannedSopImport implements ToCollection,WithValidation,WithHeadingRow, WithBatchInserts , WithChunkReading
{
    use Importable, SkipsFailures;
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $plannedSOP = PlannedSOP::where('order_id', $row['order_id'])->first();
            if ($plannedSOP) {
                $plannedSOP->update(['dispatch_against_plan' => (int) ($row['dispatch_against_plan'] ?? 0)]);
            }
        }
    }

    public function rules(): array
    {
        return [
            // 'itm_desc' => 'required|string',
            // 'itm_grp_name' => 'required|string',
            // 'warehouse_name' => 'required|string',
            // 'branch_id' => 'required|integer',
            // 'instock_qty' => 'required|numeric',
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
