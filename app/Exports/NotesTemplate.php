<?php

namespace App\Exports;

use App\Models\Notes;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;

class NotesTemplate implements FromCollection,WithHeadings,ShouldAutoSize
{
    public function collection()
    {
        return Notes::select('note', 'user_id', 'status_id')->limit(0)->get();   
    }

    public function headings(): array
    {
        return ['note', 'user_id', 'status_id'];
    }

}