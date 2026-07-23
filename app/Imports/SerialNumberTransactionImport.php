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
use Validator;

class SerialNumberTransactionImport implements ToCollection, WithValidation, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    use Importable;

    public function model(array $row)
    {
        return new Product([
            //
        ]);
    }
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $excelDate = $row['invoice_date'] - 25569; // Adjust for Excel's epoch
            $unixTimestamp = strtotime('+' . $excelDate . ' days', strtotime('1970-01-01'));
            $carbonDate = Carbon::createFromTimestamp($unixTimestamp);
            $all_serial_no = explode(',', $row['serial_no']);
            $product = Product::where('product_code', $row['product_code'])->first();
            foreach ($all_serial_no as $serial_no) {
                if ($serial_no != '' && $serial_no != NULL) {
                    $product = Services::updateOrCreate([
                        'serial_no' => isset($serial_no) ? $serial_no : null,
                        'invoice_no' => isset($row['invoice_no']) ? $row['invoice_no'] : '',

                    ], [
                        'product_name' => isset($product->product_name) ? ucfirst($product->product_name) : '',
                        'product_code' => isset($row['product_code']) ? $row['product_code'] : '',
                        'product_description' => isset($product->description) ? ucfirst($product->description) : '',
                        'product_store' => isset($row['store']) ? $row['store'] : '',
                        'invoice_date' => isset($row['invoice_date']) ? date('Y-m-d', strtotime($carbonDate)) : '',
                        'branch_code' => isset($row['branch_code']) ? $row['branch_code'] : '',
                        'party_name' => isset($row['party_name']) ? ucfirst($row['party_name']) : '',
                        'customer_id' => isset($row['customer_id']) ? $row['customer_id'] : '',
                        'bp_code' => isset($row['bp_code']) ? $row['bp_code'] : '',
                        'qty' => isset($row['qty']) ? $row['qty'] : 0,
                        'description' => isset($row['description']) ? ucfirst($row['description']) : '',
                        'group' => isset($row['group']) ? $row['group'] : null,
                        'new_group' => isset($row['new_group']) ? $row['new_group'] : null,
                        'narration' => isset($row['narration']) ? $row['narration'] : null,
                        'created_by' => Auth::user()->id,
                        'created_at' => getcurentDateTime(),
                        'updated_at' => getcurentDateTime(),
                    ]);
                }
            }
        }
    }
    public function rules(): array
    {
        $rules = [
            'product_name' => 'required|string|regex:/[a-zA-Z0-9\s]+/',
            'invoice_no' => 'required',
            // 'invoice_no' => 'required|unique:services,invoice_no',
            'product_code' => [
                'required',
                Rule::exists('products', 'product_code')
            ],
            'branch_code' => [
                'required',
                Rule::exists('branches', 'branch_code')
            ],
        ];
        return $rules;
    }

    public function customValidationMessages()
    {
        return [
            'product_name.required' => 'The product name is required.',
            'product_name.string' => 'The product name must be a string.',
            'product_name.regex' => 'The product name format is invalid.',
            'invoice_no.required' => 'The invoice number is required.',
            'invoice_no.unique' => 'The invoice number has already been taken.',
            'product_code.required' => 'The product code is required.',
            'product_code.exists' => 'The selected product code is invalid.',
            'branch_code.required' => 'The branch code is required.',
            'branch_code.exists' => 'The selected branch code is invalid.'
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
