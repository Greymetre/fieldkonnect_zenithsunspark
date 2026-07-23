<?php

namespace App\Exports;

use App\Models\Tasks;
use App\Models\TaskAssignment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Carbon\Carbon;


class TasksExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping, WithEvents
{
    protected $tasks;
    protected $maxComments = 0;

    public function __construct()
    {
        $this->tasks = $this->getTasks();

        // Calculate max comments across all tasks
        $this->maxComments = $this->tasks->map(function ($task) {
            return $task->latest_comments->count();
        })->max();
    }

    public function getTasks()
    {
        $assignedTaskIds = TaskAssignment::where('user_id', auth()->user()->id)->pluck('task_id');
        $userids = getUsersReportingToAuth();

        $query = Tasks::with([
            'users',
            'statusname',
            'task_priority',
            'task_department',
            'latest_comments'
        ])->whereHas('users', function ($query) use ($userids) {
            if (!auth()->user()->hasRole('superadmin') && !auth()->user()->hasRole('Admin')) {
                // Optional filter if needed
            }
        });

        if (!auth()->user()->hasRole('superadmin')) {
            $query->whereIn('id', $assignedTaskIds);
        }

        return $query->latest()->get();
    }

    public function collection()
    {
        return $this->tasks;
    }

    public function headings(): array
    {
        $baseHeadings = [
            'Assigned Date', 'Assigned To', 'Task Department', 'Task Type','Task Type Name', 'Task Title',
            'Priority', 'Status', 'Assigned By', 'Descriptions',
            'Due Date & Time', 'Open Date & Time', 'In Progress Date & Time',
            'Complete Date & Time', 'ReOpen Date & Time', 'Task TAT (hrs)', 'Due tat hrs',
        ];

        // Dynamically add comment headings
        for ($i = 1; $i <= $this->maxComments; $i++) {
            $baseHeadings[] = "Comment-$i";
        }

        return $baseHeadings;
    }

    public function map($data): array
    {
        $assignedUsers = TaskAssignment::with('users')->where('task_id', $data->id)->get();
        $userNames = $assignedUsers->pluck('users.name')->filter()->implode(', ');
        $taskTypeName='';
        if($data->task_type =='customer'){
            $taskTypeName= optional($data->customers)->name;
        }elseif($data->task_type =='project'){
            $taskTypeName= optional($data->project)->name;
        }elseif($data->task_type =='lead'){
            $taskTypeName= optional($data->lead)->company_name;
        }        
            
        $row = [
            $this->formatDate($data->created_at),
            $userNames,
            $data->task_department->name ?? '',
            $data->task_type ?? '',
            $taskTypeName,
            $data->title ?? '',
            $data->task_priority->name ?? '',
            $data->task_status ?? '',
            $data->users->name ?? '',
            strip_tags($data->descriptions),
            $this->formatDate($data->due_datetime),
            $this->formatDate($data->open_datetime),
            $this->formatDate($data->inprogress_datetime),
            $this->formatDate($data->completed_at),
            $this->formatDate($data->reopen_datetime),
            (string)($data->completed_at && $data->created_at)? Carbon::parse($data->created_at)->diff(Carbon::parse($data->completed_at))->format('%H:%I:%S'): '',
            (string)($data->completed_at && $data->due_datetime)? Carbon::parse($data->due_datetime)->diff(Carbon::parse($data->completed_at))->format('%H:%I:%S'): '',

        ];

        // Add dynamic comments
        for ($i = 0; $i < $this->maxComments; $i++) {
            $comment = $data->latest_comments[$i]->comment ?? '';
            $row[] = strip_tags($comment);
        }

        return $row;
    }

    private function formatDate($value)
    {
        return isset($value) ? ExcelDate::PHPToExcel($value) : '';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestColumn = $sheet->getHighestColumn();
                $highestRow = $sheet->getHighestRow();

                // 1. Style the header row
                $sheet->getStyle("A1:{$highestColumn}1")->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ]
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFEFEFEF'],
                    ],
                ]);

                // 2. Style all content rows
                $sheet->getStyle("A2:{$highestColumn}{$highestRow}")->applyFromArray([
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ]
                    ],
                ]);

                // 3. Format date columns
                $dateColumns = ['A', 'J', 'K', 'L', 'M', 'N','O'];
                foreach ($dateColumns as $col) {
                    $sheet->getStyle("{$col}2:{$col}{$highestRow}")
                        ->getNumberFormat()
                        ->setFormatCode('dd-mm-yyyy hh:mm');
                }
            },
        ];
    }

}
