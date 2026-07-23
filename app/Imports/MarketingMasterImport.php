<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\ProductDetails;
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
use App\Models\Services;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\Marketing;
use App\Models\User;
use Validator;

class MarketingMasterImport implements ToCollection, WithValidation, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    use Importable;

    protected $googleDrivelink;
    protected $countOfParticipant;

    public function __construct($googleDrivelink, $countOfParticipant)
    {
        $this->googleDrivelink = $googleDrivelink;
        $this->countOfParticipant = $countOfParticipant;
    }

    public function model(array $row)
    {
        return new Marketing([
            //
        ]);
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if (is_numeric($row['event_date'])) {
                $excelDate = $row['event_date'] - 25569; // Adjust for Excel's epoch
                $unixTimestamp = strtotime('+' . $excelDate . ' days', strtotime('1970-01-01'));
                $row['event_date'] = !empty($row['event_date']) ? Carbon::createFromTimestamp($unixTimestamp) : '';
            }

            if(isset($row['delete']) && $row['delete'] == 'Y') {
                Marketing::where('id', $row['id'])->delete();
            }else{
                Marketing::updateOrCreate([
                    'mob_no_of_participant' => $row['mob_no_of_participant'],
                ], [
                    'event_date' => Carbon::parse($row['event_date'])->toDateString(),
                    'division' => $row['division'],
                    'event_center' => $row['event_center'],
                    'place_of_participant' => $row['place_of_participant'],
                    'event_district' => $row['event_district'],
                    'state' => $row['state'],
                    'event_under_type' => $row['event_under_type'],
                    'event_under_name' => $row['event_under_name'],
                    'branch' => $row['branch'],
                    'responsible_for_event' => $row['name_responsible_for_event'],
                    'branding_team_member' => $row['branding_team_member'],
                    'name_of_participant' => $row['name_of_participant'],
                    'category_of_participant' => $row['category_of_participant'],
                    'google_drivelink' => $this->googleDrivelink,
                    'count_of_participant' => $this->countOfParticipant,
                    'created_by' => Auth::user()->id
                ]);
            }

        }
    }

    public function rules(): array
    {
        $rules = [
            'mob_no_of_participant' => [
                'required',
                'regex:/^[0-9]{10}$/'
            ],
            'branch' => 'required|exists:branches,branch_name',
            'state' => 'required|exists:states,state_name',
            'category_of_participant' => 'required|in:Plumber,Mechanic,Village influencer,Retailer,Exhibition Visitors,Electrician',
            'division' => 'required|exists:divisions,division_name',
            'event_date' => 'required|date|before_or_equal:today',
        ];
        return $rules;
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
