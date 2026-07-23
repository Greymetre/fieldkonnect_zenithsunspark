<?php

namespace App\Imports;

use App\Models\LeadContact;
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

class LeadsContactsImport implements ToCollection,WithValidation,WithHeadingRow, WithBatchInserts , WithChunkReading
{
    use Importable, SkipsFailures;


    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            LeadContact::create([
                'name' => $row['name'],
                'title' => $row['title'],
                'phone_number' => $row['phone_number'],
                'email' => $row['email'],
                'lead_id' => $row['lead_id'],
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'name' => 'required',
            'title' => 'required',
            'phone_number' => 'required',
            'email' => 'required',
            'lead_id' => 'required|exists:leads,id',
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
