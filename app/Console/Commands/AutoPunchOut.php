<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Attendance;

class AutoPunchOut extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'punchout:name';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto PunchOut by System';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $attendances = Attendance::whereNull('punchout_date')->select('id','punchin_date','punchout_latitude','punchout_longitude','punchin_longitude','punchin_latitude','punchout_address')->get();
        foreach ($attendances as $key => $attendance) {
            Attendance::where('id','=',$attendance['id'])->update([
                'punchout_date' => $attendance['punchin_date'],
                'punchout_time' => date('H:i:s'),
                'punchin_address' => ($attendance['punchin_address'] == '') ? getLatLongToAddress($attendance['punchin_latitude'] , $attendance['punchin_longitude']) : $attendance['punchin_address'],
                'punchout_address' => ($attendance['punchout_address'] == '') ? getLatLongToAddress($attendance['punchout_latitude'] , $attendance['punchout_longitude']) : $attendance['punchout_address'],
                'punchout_summary' => 'Auto PunchOut'
            ]);
        }
        return Command::SUCCESS;
    }
}
