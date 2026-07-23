<?php
namespace App\Imports;

use App\Models\SchemeDetails;
use App\Models\TransactionHistory;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

use Maatwebsite\Excel\Validators\Failure;

use Log;
use Illuminate\Support\Facades\Redirect;
use App\Models\Services;
use AWS\CRT\HTTP\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Validator;

class MainTransactionImport implements ToCollection, WithValidation, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    use Importable;

    public function model(array $row)
    {
        return new TransactionHistory([
            //
        ]);
    }
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $row['coupon_code'] = explode(',', $row['coupon_code']);
            $nonNullCoupenCodes = array_filter($row['coupon_code'], function ($value) {
                return !is_null($value);
            });
            $expire_schemes = array();
            $notInsert = array();
            foreach ($nonNullCoupenCodes as $nonNullCoupenCode) {
                $exists = TransactionHistory::where('coupon_code', $nonNullCoupenCode)->exists();
                $notexists = Services::where('serial_no', $nonNullCoupenCode)->exists();
                if (!$exists && $notexists) {
                    $scheme = Services::where('serial_no', $nonNullCoupenCode)->first();
                    $scheme_details = SchemeDetails::where('product_id', $scheme->product->id)->first();
                    $start_date = Carbon::createFromFormat('Y-m-d', $scheme_details->scheme->start_date);
                    $end_date = Carbon::createFromFormat('Y-m-d', $scheme_details->scheme->end_date);
                    $current_date = Carbon::today();
                    if ($current_date->isSameDay($start_date) || ($current_date->gte($start_date) && $current_date->lte($end_date))) {
                        $active_point = ($scheme_details) ? $scheme_details->active_point : NULL;
                        $provision_point = ($scheme_details) ? $scheme_details->provision_point : NULL;
                        $point = ($scheme_details) ? $scheme_details->points : NULL;
                    } else {
                        array_push($expire_schemes, $nonNullCoupenCode);
                        $active_point = '0';
                        $provision_point = '0';
                        $point = '0';
                    }
                    $tHistory = TransactionHistory::create([
                        'customer_id' => $request->customer_id,
                        'coupon_code' => $nonNullCoupenCode,
                        'scheme_id' => $scheme_details->scheme_id,
                        'active_point' => $active_point,
                        'provision_point' => $provision_point,
                        'point' => $point,
                        'remark' => 'Coupon scan',
                        'created_by' => auth()->user()->id,
                    ]);
                } else {
                    if ($exists) {
                        $push_is = $nonNullCoupenCode . ' - already Scanned ';
                    } elseif (!$notexists) {
                        $push_is = $nonNullCoupenCode . ' - Invalid ';
                    }
                    array_push($notInsert, $push_is);
                }
            }
            if (count($expire_schemes) > 0) {
                if (count($notInsert) > 0) {
                    $_SESSION['tr_msg'] = 'Transaction History Store Successfully but coupon code (' . implode(',', $expire_schemes) . ') scheme has either expired or has not started yet so you earned 0 point And also check (' . implode(',', $notInsert) . ').';
                }
                $_SESSION['tr_msg'] = 'Transaction History Store Successfully but coupon code (' . implode(',', $expire_schemes) . ') scheme has either expired or has not started yet so you earned 0 point.';
            } else {
                if (count($notInsert) > 0) {
                    $_SESSION['tr_msg'] = 'Transaction History Store Successfully And also check (' . implode(',', $notInsert) . ').';
                }else{
                    $_SESSION['tr_msg'] = 'Transaction History Store Successfully';
                }
            }
        }
    }
    public function rules(): array
    {
        $rules = [
            'customer_id' => 'required|exists:customers,id',
            'coupon_code' => 'required',
        ];
        return $rules;
    }

    public function customValidationMessages()
    {
        return [
            'customer_id.required' => 'The customer id is required.',
            'customer_id.exists' => 'Customer not found.',
            'coupon_code.required' => 'The coupon code is required.'
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
