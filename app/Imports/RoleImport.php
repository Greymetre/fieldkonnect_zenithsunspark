<?php

namespace App\Imports;

use App\Models\Role;
use Maatwebsite\Excel\Concerns\ToModel;

class RoleImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Role([
            //
        ]);
    }
}
