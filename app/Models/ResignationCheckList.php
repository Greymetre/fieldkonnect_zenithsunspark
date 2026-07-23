<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResignationCheckList extends Model
{
    use HasFactory;

    protected $fillable = ['resignation_id', 'document_file', 'exit_interview', 'advance', 'laptop', 'sim_card', 'keys', 'visiting_card', 'income_tax', 'laptop_bag', 'expense_voucher', 'crm_id', 'unpaid_salary', 'data_email', 'id_card', 'payable_expense', 'pen_drive', 'bouns', 'created_at', 'updated_at'];

    public $timestamps = true;

    public function resignation()
    {
        return $this->belongsTo(Resignation::class, 'resignation_id', 'id');
    }
}
