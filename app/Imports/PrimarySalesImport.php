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
use App\Models\PrimarySales;
use App\Models\User;
use Validator;

class PrimarySalesImport implements ToCollection, WithValidation, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    use Importable;

    public function model(array $row)
    {
        return new PrimarySales([
            //
        ]);
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if (is_numeric($row['invoice_date'])) {
                $excelDate = $row['invoice_date'] - 25569; // Adjust for Excel's epoch
                $unixTimestamp = strtotime('+' . $excelDate . ' days', strtotime('1970-01-01'));
                $row['invoice_date'] = !empty($row['invoice_date']) ? Carbon::createFromTimestamp($unixTimestamp) : '';
            }
            if (isset($row['id']) && !empty($row['id'])) {
                if ($row['delete_this'] && !empty($row['delete_this']) && $row['delete_this'] == '1') {
                    PrimarySales::where('id', $row['id'])->delete();
                } else {
                    $salesTargetUsers = PrimarySales::where('id', $row['id'])->update([
                        'invoiceno' => $row['invoice_no'],
                        'invoice_date' => $row['invoice_date'],
                        'month' => $row['month'],
                        'division' => $row['div'],
                        'bp_code' => $row['bp_code'],
                        'dealer' => $row['dealer'],
                        'city' => $row['city'],
                        'state' => $row['state'],
                        'final_branch' => $row['final_branch'],
                        'branch_id' => $row['branch_id'],
                        'sales_person' => $row['sales_person'],
                        'emp_code' => $row['emp_code'],
                        'product_name' => $row['product_name'],
                        'model_name' => $row['model_name'],
                        'quantity' => $row['quantity'],
                        'rate' => $row['rate'],
                        'net_amount' => $row['net_amount'],
                        'tax_amount' => $row['tax'],
                        'cgst_amount' => $row['cgst_amt'],
                        'sgst_amount' => $row['sgst_amt'],
                        'igst_amount' => $row['igst_amt'],
                        'total_amount' => $row['total'],
                        'store_name' => $row['store_name'],
                        'new_group' => $row['group'],
                        'branch' => $row['branch'],
                        'new_group_name' => $row['new_group_name'],
                        'product_id' => $row['product_id'] ?? NULL,
                        'customer_id' => $row['customer_id'] ?? NULL,
                        'sap_code' => $row['product_sap_code'] ?? NULL,
                        'new_product' => $row['new_product'] ?? NULL,
                        'new_dealer' => $row['new_dealer'] ?? NULL,
                        'group_1' => $row['group_1'] ?? NULL,
                        'group_2' => $row['group_2'] ?? NULL,
                        'group_3' => $row['group_3'] ?? NULL,
                        'group_4' => $row['group_4'] ?? NULL,
                    ]);
                }
            } else {
                $salesTargetUsers = PrimarySales::create([
                    'invoiceno' => $row['invoice_no'],
                    'invoice_date' => $row['invoice_date'],
                    'month' => $row['month'],
                    'division' => $row['div'],
                    'bp_code' => $row['bp_code'],
                    'dealer' => $row['dealer'],
                    'city' => $row['city'],
                    'state' => $row['state'],
                    'final_branch' => $row['final_branch'],
                    'branch_id' => $row['branch_id'],
                    'sales_person' => $row['sales_person'],
                    'emp_code' => $row['emp_code'],
                    'product_name' => $row['product_name'],
                    'model_name' => $row['model_name'],
                    'quantity' => $row['quantity'],
                    'rate' => $row['rate'],
                    'net_amount' => $row['net_amount'],
                    'tax_amount' => $row['tax'],
                    'cgst_amount' => $row['cgst_amt'],
                    'sgst_amount' => $row['sgst_amt'],
                    'igst_amount' => $row['igst_amt'],
                    'total_amount' => $row['total'],
                    'store_name' => $row['store_name'],
                    'new_group' => $row['group'],
                    'branch' => $row['branch'],
                    'new_group_name' => $row['new_group_name'],
                    'product_id' => $row['product_id'] ?? NULL,
                    'customer_id' => $row['customer_id'] ?? NULL,
                    'sap_code' => $row['product_sap_code'] ?? NULL,
                    'new' => $row['new'] ?? NULL,
                    'group_1' => $row['group_1'] ?? NULL,
                    'group_2' => $row['group_2'] ?? NULL,
                    'group_3' => $row['group_3'] ?? NULL,
                    'group_4' => $row['group_4'] ?? NULL,
                ]);
            }
        }
    }

    public function rules(): array
    {
        $rules = [
            'invoice_no' => 'required',
        ];
        return $rules;
    }

    public function customValidationMessages()
    {
        return [
            'invoice_no.required' => 'The invoiceno is required.',
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
