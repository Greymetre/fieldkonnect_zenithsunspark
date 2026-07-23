<?php

namespace App\Exports;

use App\Models\SurveyData;
use App\Models\Field;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SurveyExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping
{
    public function __construct($request)
    {
        $this->module = isset($request->module) ? $request->module : 1 ;
        $this->headingsarray = Field::where(function ($query) {
            $query->where('module', '=', $this->module);
        })->select('id','field_name','label_name')->get();
    }

    public function collection()
    {
        $serveys = SurveyData::with('customers')->where(function ($query) {
            $query->whereHas('customers', function($query){
                $query->where('customertype', '=', $this->module);
            });
        })
        ->select('customer_id', DB::raw('count(value) as total_ans'))
        ->groupBy('customer_id')->get();

        $multiplied = $serveys->map(function ($item, $key) {
            $customerdatas = SurveyData::where('customer_id', '=', $item->customer_id)->select('field_id','value','created_at','created_by')->get();
            $item[wordwrap(strtolower('ID'), 1, '_', 0)] = isset($item->customer_id) ? $item->customer_id : '';
            $item[wordwrap(strtolower('Firm Name'), 1, '_', 0)] = isset($item['customers']['name']) ? $item['customers']['name'] : '';
            $item[wordwrap(strtolower('FOS'), 1, '_', 0)] = isset($customerdatas[0]['createdbyname']['name']) ? $customerdatas[0]['createdbyname']['name'] : '';
            $item[wordwrap(strtolower('Created date'), 1, '_', 0)] = isset($customerdatas[0]['created_at']) ? $customerdatas[0]['created_at'] : '';
            foreach ($this->headingsarray as $key => $field) {
                $fieldans = $customerdatas->where('field_id',$field->id)->pluck('value')->first();
                $item[wordwrap(strtolower($field->field_name), 1, '_', 0)] = isset($fieldans) ? $fieldans : '';
            }
            return $item;
        });
        return $multiplied;   
    }

    public function headings(): array
    {
        $field = array( 
            '0' => 'ID',
            '1' => 'Firm Name',
            '2' => 'FOS',
            '3' => 'Created date'
            );
        $headingdata = $this->headingsarray->pluck('label_name')->toArray();
        return array_unique(array_merge($field, $headingdata), SORT_REGULAR);
    }

    public function map($data): array
    {
        $array = array();
        array_push($array, isset($data->customer_id) ? $data->customer_id : '');
        array_push($array, isset($data->firm_name) ? $data['firm_name'] : '');
        array_push($array, isset($data->fos) ? $data->fos : '');
        array_push($array, isset($data->fos) ? $data['created_date'] : '');
        foreach ($this->headingsarray as $key => $field) {
            array_push($array, isset($data[wordwrap(strtolower($field->field_name), 1, '_', 0)]) ? $data[wordwrap(strtolower($field->field_name), 1, '_', 0)] : '');
        }
        return $array;
    }

}