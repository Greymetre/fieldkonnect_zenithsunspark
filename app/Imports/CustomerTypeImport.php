<?php

namespace App\Imports;

use App\Models\CustomerType;
use Maatwebsite\Excel\Concerns\ToModel;

class CustomerTypeImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new CustomerType([
            //
        ]);
    }
}
