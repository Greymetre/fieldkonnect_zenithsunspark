<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Attendance;
use App\Models\CheckIn;
class AddressUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'addressupdate:name';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Checkin and Checout Address';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $punchins = Attendance::whereNotNull(['punchin_latitude','punchin_longitude'])
                                ->where('punchin_address','=','')
                                ->select('id','punchin_longitude','punchin_latitude')->get();
        foreach ($punchins as $key => $punchin) {
            Attendance::where('id','=',$punchin['id'])->update([
                'punchin_address' => getLatLongToAddress($punchin['punchin_latitude'] , $punchin['punchin_longitude']),
            ]);
        }
        $punchouts = Attendance::whereNotNull(['punchout_latitude','punchout_longitude'])
                                ->where('punchout_address','=','')
                                ->select('id','punchout_latitude','punchout_longitude')->get();
        foreach ($punchouts as $key => $punchout) {
            Attendance::where('id','=',$punchout['id'])->update([
                'punchout_address' => getLatLongToAddress($punchout['punchout_latitude'] , $punchout['punchout_longitude']),
            ]);
        }
        $checkins = CheckIn::whereNotNull(['checkin_latitude','checkin_longitude'])
                                ->where('checkin_address','=','')
                                ->select('id','checkin_latitude','checkin_longitude')->get();
        foreach ($checkins as $key => $checkin) {
            CheckIn::where('id','=',$checkin['id'])->update([
                'checkin_address' => getLatLongToAddress($checkin['checkin_latitude'] , $checkin['checkin_longitude']),
            ]);
        }
        $checkouts = CheckIn::whereNotNull(['checkout_latitude','checkout_longitude'])
                                ->where('checkout_address','=','')
                                ->select('id','checkout_latitude','checkout_longitude')->get();
        foreach ($checkouts as $key => $checkout) {
            CheckIn::where('id','=',$checkout['id'])->update([
                'checkout_address' => getLatLongToAddress($checkout['checkout_latitude'] , $checkout['checkout_longitude']),
            ]);
        }
        return Command::SUCCESS;
    }
}
