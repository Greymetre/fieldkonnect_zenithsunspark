<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;

class ProductExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping
{
    public function __construct($request)
    {
        $this->category_id = $request->input('category_id');
        $this->active = $request->input('active');
    }

    public function collection()
    {
        $data = Product::with('productpriceinfo')->select('id','active','product_name','product_code','new_group','sub_group','expiry_interval','expiry_interval_preiod', 'display_name', 'description', 'subcategory_id', 'category_id', 'brand_id', 'product_image', 'unit_id', 'specification', 'part_no','suc_del', 'product_no', 'model_no','phase','sap_code' , 'branch_id', 'hsn_sac', 'hsn_sac_no');
        if($this->category_id && !empty($this->category_id)){
            $data->where('category_id', $this->category_id);
        }
        if($this->active && !empty($this->active)){
            $data->where('active', $this->active);
        }
        $data = $data->latest()->get();

        return $data;
    }

    public function headings(): array
    {
        return ['product_id','product_name','product_code','new_group','sub_group','expiry_interval','expiry_interval_preiod','display_name', 'description', 'subcategory_id','subcategory','category_id','category','brand_id','brand','product_image','unit_id','unit_name','mrp','price','selling_price','gst','discount','max_discount', 'hp', 'kw', 'product_stage', 'model_no','suc_del','Phase','status','Sap Code' , 'budget_for_month' , 'top_sku' , 'branch_id', 'rmc', 'hsn_sac', 'hsn_sac_no'];
    }

    public function map($data): array
    {
        return [
            $data['id'],
            $data['product_name'],
            $data['product_code'],
            $data['new_group'],
            $data['sub_group'],
            $data['expiry_interval'],
            $data['expiry_interval_preiod'],
            $data['display_name'],
            $data['description'],
            $data['subcategory_id'],
            $data['subcategories']['subcategory_name'],
            $data['category_id'],
            $data['categories']['category_name'],
            $data['brand_id'],
            $data['brands']?$data['brands']['brand_name']:'',
            $data['product_image'],
            $data['unit_id'],
            $data['unitmeasures']?$data['unitmeasures']['unit_name']:'',
            isset($data['productpriceinfo']['mrp']) ? $data['productpriceinfo']['mrp'] :'',
            isset($data['productpriceinfo']['price']) ? $data['productpriceinfo']['price'] :'',
            isset($data['productpriceinfo']['selling_price']) ? $data['productpriceinfo']['selling_price'] :'',
            isset($data['productpriceinfo']['gst']) ? $data['productpriceinfo']['gst'] : '',
            isset($data['productpriceinfo']['discount']) ? $data['productpriceinfo']['discount'] : '',
            isset($data['productpriceinfo']['max_discount']) ? $data['productpriceinfo']['max_discount'] :'' ,
            isset($data['specification']) ? $data['specification'] :'',
            isset($data['part_no']) ? $data['part_no'] :'',
            isset($data['product_no']) ? $data['product_no'] :'',            
            isset($data['model_no']) ? $data['model_no'] :'',
            $data['suc_del'],
            $data['phase'],
            $data['active'],
            $data['sap_code'],
            isset($data['productpriceinfo']['budget_for_month']) ? $data['productpriceinfo']['budget_for_month'] :'',
            isset($data['productpriceinfo']['top_sku']) ? $data['productpriceinfo']['top_sku'] :'',
            $data['branch_id'] ?? '',
            isset($data['productpriceinfo']['rmc']) ? $data['productpriceinfo']['rmc'] :'',
            isset($data['hsn_sac']) ? $data['hsn_sac'] :'',
            isset($data['hsn_sac_no']) ? $data['hsn_sac_no'] :'',
        ];
    }

}
