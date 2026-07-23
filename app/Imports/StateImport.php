<?php

namespace App\Imports;

use App\Models\State;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;
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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class StateImport implements ToCollection, WithValidation, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    use Importable, SkipsFailures;

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $data = [
                'active'      => 'Y',
                'state_name'  => isset($row['state_name']) ? ucwords(strtolower($row['state_name'])) : '',
                'country_id'  => isset($row['country_id']) ? $row['country_id'] : null,
                'gst_code'    => isset($row['gst_code']) ? $row['gst_code'] : null,
                'updated_at'  => getcurentDateTime(),
            ];

            if (isset($row['id']) && !empty($row['id'])) {
                // Update existing state
                State::where('id', $row['id'])->update($data);
            } else {
                // Create new state
                $data['created_by'] = Auth::id();
                $data['created_at'] = getcurentDateTime();

                State::create($data);
            }
        }
    }

    public function rules(): array
    {
        return [
            'state_name' => 'required|string|regex:/[a-zA-Z0-9\s]+/',
            'country_id' => 'required|exists:countries,id',
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
