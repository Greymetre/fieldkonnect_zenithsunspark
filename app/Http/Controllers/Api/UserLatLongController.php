<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserDailyLatLong;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UserLatLongController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user();
        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $date = Carbon::now()->format('Y-m-d');

        UserDailyLatLong::create([
            'user_id' => $user->id,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'date' => $date
        ]);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Data inserted successfully.',
        ], 200);

    }
}
