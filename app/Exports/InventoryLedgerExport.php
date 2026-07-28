<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class InventoryLedgerExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    private Collection $rows;

    public function __construct(Collection $rows)
    {
        $this->rows = $rows;
    }

    public function collection(): Collection
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return ['Date', 'Product', 'Warehouse', 'Type', 'Before', 'Change', 'After', 'Person', 'Reference'];
    }

    public function map($row): array
    {
        $isIn = (float) $row->quantity_in > 0;
        $change = $isIn ? (float) $row->quantity_in : -(float) $row->quantity_out;
        $before = (float) $row->balance_quantity - (float) $row->quantity_in + (float) $row->quantity_out;

        return [
            Carbon::parse($row->movement_date)->format('d M Y h:i A'),
            $row->product_name,
            $row->warehouse_name,
            $isIn ? 'IN' : 'OUT',
            $before,
            $change,
            (float) $row->balance_quantity,
            $row->transaction_type === 'sales_return' ? 'Return Accepted' : ($row->person_name ?: 'System'),
            $row->reference,
        ];
    }
}
