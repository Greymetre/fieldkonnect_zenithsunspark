<?php

namespace App\Imports;

use App\Models\GiftRedemptionDetail;
use App\Models\NeftRedemptionDetails;
use App\Models\Redemption;
use Carbon\Carbon;
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

class RedemptionImport implements ToCollection, WithValidation, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    use Importable, SkipsFailures;


    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $redemptio_is = Redemption::find($row['redemption_id']);
            if ($redemptio_is->redeem_mode == '2') {
                if ($row['status'] == '3' || $row['status'] == '4') {
                    NeftRedemptionDetails::updateOrCreate(
                        [
                            'redemption_id' => $row['redemption_id']
                        ],
                        [
                            'utr_number' => $row['transaction_id_utr_no'],
                            'tds' => $row['tds'],
                            'remark' => $row['details']
                        ]
                    );
                }
                if ($row['payment_date'] && $row['payment_date'] != null && $row['payment_date'] != '') {
                    $unixTimestamp = ($row['payment_date'] - 25569) * 86400;
                    $carbonDate = Carbon::createFromTimestamp($unixTimestamp);
                    $update_at = $carbonDate;
                } else {
                    $update_at = date('Y-m-d h:i:s', strtotime(Carbon::now()));
                }
                $updateStatus = Redemption::where('id', $row['redemption_id'])->update(['status' => $row['status'], 'invoice_number' => $row['invoice_number'], 'remark' => $row['details'], 'updated_at' => $update_at]);
            } else if ($redemptio_is->redeem_mode == '1') {
                // dd($row['approve_date']);
                if ($row['approve_date'] && $row['approve_date'] != null && $row['approve_date'] != '' && is_numeric($row['approve_date'])) {
                    $unixTimestamp = ($row['approve_date'] - 25569) * 86400;
                    $carbonDate = Carbon::createFromTimestamp($unixTimestamp);
                    $row['approve_date'] = $carbonDate;
                }
                if ($row['dispatch_date'] && $row['dispatch_date'] != null && $row['dispatch_date'] != '' && is_numeric($row['dispatch_date'])) {
                    $unixTimestamp = ($row['dispatch_date'] - 25569) * 86400;
                    $carbonDate = Carbon::createFromTimestamp($unixTimestamp);
                    $row['dispatch_date'] = $carbonDate;
                }
                if ($row['gift_recived_date'] && $row['gift_recived_date'] != null && $row['gift_recived_date'] != '' && is_numeric($row['gift_recived_date'])) {
                    $unixTimestamp = ($row['gift_recived_date'] - 25569) * 86400;
                    $carbonDate = Carbon::createFromTimestamp($unixTimestamp);
                    $row['gift_recived_date'] = $carbonDate;
                }
                Redemption::updateOrCreate(
                    [
                        'id' => $row['redemption_id']
                    ],
                    [
                        'product_send' => $row['acual_dispatch_product'],
                        'status' => $row['status'],
                        'approve_date' => $row['approve_date'],
                        'dispatch_date' => $row['dispatch_date'],
                        'dispatch_number' => $row['dispatch_number'],
                        'gift_recived_date' => $row['gift_recived_date'],
                        'remark' => $row['received_remark']
                    ]
                );
                GiftRedemptionDetail::updateOrCreate(
                    [
                        'redemption_id' => $row['redemption_id']
                    ],
                    [
                        'redemption_no' => $row['redemption_no'],
                        'purchase_rate' => $row['purchase_rate'],
                        'gst' => $row['gst'],
                        'total_purchase' => $row['total_purchase'],
                        'purchase_invoice_no' => $row['purchase_invoice_no'],
                        'purchase_return_no' => $row['purchase_return_no'],
                        'client_invoice_no' => $row['client_invoice_no']
                    ]
                );
            }
        }
    }

    public function rules(): array
    {
        return [
            'redemption_id' => [
                'required',
                'exists:redemptions,id',
            ],
            'status' => 'required|numeric',
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
