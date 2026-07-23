<?php

namespace App\Exports;

use App\Models\CustomerType;
use Maatwebsite\Excel\Concerns\FromCollection;

class CustomerTypeTemplate implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return CustomerType::all();
    }
}
