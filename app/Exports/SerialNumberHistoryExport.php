<?php

namespace App\Exports;

use App\Models\Customers;
use App\Models\OrderDetails;
use App\Models\Product;
use App\Models\Services;
use App\Models\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\Auth;


class SerialNumberHistoryExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping, WithEvents
{
    public function __construct($request)
    {
        $this->user_id = $request->input('user_id');
        $this->start_date = $request->input('start_date');
        $this->end_date = $request->input('end_date');
    }

    public function collection()
    {

        $query = Services::with('product', 'warrantyDetails');
        if($this->start_date && $this->start_date != '' && $this->start_date != NULL && $this->end_date && $this->end_date != '' && $this->end_date != NULL){
            $query->whereBetween('created_at', [$this->start_date, $this->end_date]);
        }
        $query = $query->latest()->limit('1000')->get();

        return $query;
    }

    public function headings(): array
    {
        return ['Serial Number', 'Group', 'Sub Group', 'Product Code', 'Product Name', 'Product Model', 'Party Nmae', 'customer_id', 'bp_code', 'Invoice Number', 'Invoice Dtae', 'Expiry Date', 'Warranty Status'];
    }

    public function map($data): array
    {
        $product = Product::where('product_code', $data->product_code)->first();
        if ($product->expiry_interval && $product->expiry_interval != null && $product->expiry_interval != '' && $product->expiry_interval_preiod && $product->expiry_interval_preiod > 0 && $product->expiry_interval_preiod != null) {
            $initialDate = Carbon::parse($data->invoice_date);

            $expiryDate = $initialDate->add($product->expiry_interval_preiod, strtolower($product->expiry_interval));

            $exp =  date("d/m/Y", strtotime($expiryDate)) ?? '';

            // return $expiryDate->toDateString();
        } else {
            $exp = 'No Expiry Date';
        }

        return [
            $data['serial_no'] ?? '',
            $data['group'] ?? '',
            $data['new_group'] ?? '',
            $data['product_code'] ?? '',
            $data['product_name'] ?? '',
            $data['??']['name'] ?? '',
            $data['party_name'] ?? '',
            $data['customer_id'] ?? '',
            $data['bp_code'] ?? '',
            $data['invoice_no'] ?? '',
            $data['invoice_date'] ?date("d/M/Y", strtotime($data->invoice_date)):'',
            $exp,
            $data['warrantyDetails']?'Active':'Not Active',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $lastRow = $event->sheet->getHighestDataRow() + 2;

                $event->sheet->getStyle('A1:K1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '336677'],
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);

                $event->sheet->getStyle('A' . $lastRow . ':AA' . $lastRow)->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                    ],
                    'borders' => [
                        'top' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'], // Border color
                        ],
                    ],
                ]);
            },
        ];
    }
}
