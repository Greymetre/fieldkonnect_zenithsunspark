<?php

namespace App\Imports;

use App\Models\Payment;
use Maatwebsite\Excel\Concerns\ToModel;

class PaymentImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Payment([
            //
        ]);
    }
}
