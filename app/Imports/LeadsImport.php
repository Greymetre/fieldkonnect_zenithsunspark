<?php

namespace App\Imports;

use App\Models\Address;
use App\Models\City;
use App\Models\District;
use App\Models\Lead;
use App\Models\LeadContact;
use App\Models\LeadNote;
use App\Models\Notes;
use App\Models\Pincode;
use App\Models\State;
use App\Models\Status;
use App\Models\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class LeadsImport implements ToCollection, WithValidation, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    use Importable, SkipsFailures;


    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {

            if (isset($row['lead_generation_date']) && is_numeric($row['lead_generation_date'])) {
                $excelDate = $row['lead_generation_date'] - 25569; // Adjust for Excel's epoch
                $unixTimestamp = strtotime('+' . $excelDate . ' days', strtotime('1970-01-01'));
                $row['lead_generation_date'] = !empty($row['lead_generation_date']) ? Carbon::createFromTimestamp($unixTimestamp)->toDateString() : '';
            }
            $status = Status::where('display_name', $row['lead_type'])->first()?->id ?? 0;
            $expectedKeys = [
                'lead_generation_date',
                'firm_name',
                'customer_name',
                'customer_number',
                'email',
                'lead_source',
                'pincode',
                'place',
                'city',
                'district',
                'state',
                'address',
                'lead_type',
                'assignee',
                'note',
                'website'
            ];

            // Step 1: Collect other (unexpected) keys without null/empty key or value
            $otherData = collect($row)->filter(function ($value, $key) use ($expectedKeys) {
                return !in_array($key, $expectedKeys) && !is_null($key) && $key !== '' && !is_null($value) && $value !== '';
            })->toArray();
            $otherData = json_encode($otherData, JSON_UNESCAPED_UNICODE);
            $lead = Lead::create([
                'company_name' => $row['firm_name'],
                'company_url' => $row['website'],
                'status' => $status,
                'created_by' => Auth::id(),
                'lead_generation_date' => date('Y-m-d', strtotime($row['lead_generation_date'] ?? time())),
                'lead_source' => $row['lead_source'],
                'assign_to' => User::where('name', $row['assignee'])->first()->id ?? null,
                'others' => $otherData,
            ]);
            if ($lead->id) {
                Address::create([
                    'model_type' => 'App\Models\Lead',
                    'model_id' => $lead->id,
                    'address1' => $row['address'] ?? 'N/A',
                    'address2' => $row['place'] ?? 'N/A',
                    'country_id' => 1,
                    'pincode_id' => $row['pincode'] ? Pincode::where('pincode', $row['pincode'])->first()->id : null,
                    'state_id' => $row['state'] ? State::where('state_name', $row['state'])->first()->id : null,
                    'city_id' => $row['city'] ? City::where('city_name', $row['city'])->first()->id : null,
                    'district_id' => $row['district'] ? District::where('district_name', $row['district'])->first()->id : null,
                    'created_by' => Auth::id()
                ]);
                $category = LeadContact::create([
                    'name' => $row['customer_name'],
                    'phone_number' => (string)$row['customer_number'],
                    'email' => $row['email'],
                    'lead_source' => $row['lead_source'],
                    'lead_id' => $lead->id,
                    'created_by' => Auth::id()
                ]);
                if (isset($row['note']) && !empty($row['note'])) {
                    $note = LeadNote::create([
                        'note' => $row['note'],
                        'lead_id' => $lead->id,
                        'created_by' => Auth::id()
                    ]);
                }
            }
        }
        $assignees = $rows->pluck('assignee')->countBy()->toArray();
        foreach ($assignees as $assignee => $count) {
            $user = User::where('name', $assignee)->first();
            if (!empty($user)) {
                SendPushNotification($user->id, '🟢 You have been assigned ' . $count . ' new leads.');
                StoreLeadNotification(null, 'Assigned Lead', '🟢 You have been assigned ' . $count . ' new leads.', $user->id);
            }
        }
    }

    public function rules(): array
    {
        return [
            'firm_name' => 'required',
            'customer_name' => 'required',
            'pincode' => 'nullable|exists:pincodes,pincode',
            'city' => 'nullable|exists:cities,city_name',
            'district' => 'nullable|exists:districts,district_name',
            'state' => 'nullable|exists:states,state_name',
            'lead_type' => 'nullable|exists:statuses,display_name',
            'lead_source' => 'nullable|in:Google,Indiamart,Justdial,Instagram,Facebook,Self',
            'assignee' => 'nullable|exists:users,name',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'firm_name.required' => 'Firm name is required.',
            'customer_name.required' => 'Customer name is required.',
            'customer_number.required' => 'Customer number is required.',

            'pincode.exists' => 'The selected pincode is not valid.',
            'city.exists' => 'The selected city does not exist in our records.',
            'district.exists' => 'The selected district does not exist in our records.',
            'state.exists' => 'The selected state does not exist in our records.',

            'lead_type.exists' => 'The lead type must be a valid status name.',
            'lead_source.in' => 'The lead source must be one of: Google, Indiamart, Justdial, Instagram, Facebook, Self.',
            'assignee.exists' => 'The selected assignee name was not found in users.',
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
