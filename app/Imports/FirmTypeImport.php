<?php

namespace App\Imports;

use App\Models\FirmType;
use Maatwebsite\Excel\Concerns\ToModel;

class FirmTypeImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new FirmType([
            //
        ]);
    }
}
