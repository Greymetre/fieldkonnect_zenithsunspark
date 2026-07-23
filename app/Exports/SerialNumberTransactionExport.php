<?php

namespace App\Exports;

use App\Models\Services;
use App\Models\Branch;
use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;
use DB;

class SerialNumberTransactionExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping
{

    public function __construct($request)
    {    
        $this->startdate = $request->input('start_date');
        $this->enddate = $request->input('end_date');
        $this->product_id = $request->input('product_id');
        $this->branch_id = $request->input('branch_id');  
      
    }
    public function collection()
    {
        $data = Services::select(
            'product_code',
            'invoice_no',
            'invoice_date',
            'branch_code',
            'party_name',
            'customer_id',
            'bp_code',
            'product_name',
            'product_description',
            'product_store',
            'qty',
            'group',
            'new_group',
            DB::raw('GROUP_CONCAT(serial_no) as serial_no'),
            DB::raw('count(serial_no) as serial_no_count'),
            DB::raw('GROUP_CONCAT(narration) as narration')
        );
        if($this->branch_id && $this->branch_id != null && $this->branch_id != ''){
            $branche_code = Branch::where('id', $this->branch_id)->value('branch_code');
            $data = $data->where('branch_code', $branche_code);
        }
        if($this->product_id && $this->product_id != null && $this->product_id != ''){
            $product_code = Product::where('id', $this->product_id)->value('product_code');
            $data = $data->where('product_code', $product_code);
        }
        if($this->startdate && $this->startdate != null && $this->startdate != '' && $this->enddate && $this->enddate != null && $this->enddate != ''){
            $data = $data->whereBetween('invoice_date', [$this->startdate, $this->enddate]);
        }
        $data = $data->groupBy('product_code', 'invoice_no','invoice_date' ,'branch_code','party_name', 'customer_id', 'bp_code', 'product_description', 'product_store','product_name','group','new_group','qty')->limit(1000)->get();
        return $data;
    }

    public function headings(): array
    {
        return ['Invoice No','Invoice Date','Party Name', 'customer_id', 'bp_code','Product Code','Product Name','Qty','Group','Branch Code','Serial No.','Narration','Product Description','Store','New Group'];
    }

    public function map($data): array
    {
        return [
            $data['invoice_no'],
            isset($data['invoice_date']) ? date("d-m-Y", strtotime($data['invoice_date'])) :'',
            $data['party_name'],
            $data['customer_id'],
            $data['bp_code'],
            $data['product_code'],
            $data['product_name'],
            $data['serial_no_count'],
            $data['group'],
            $data['branch_code'],
            $data['serial_no'],
            $data['narration'],
            $data['product_description'],
            $data['product_store'],
            $data['new_group']
        ];
    }

}
