<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Stevebauman\Location\Facades\Location;
use Jenssegers\Agent\Agent;
use Torann\GeoIP\Facades\GeoIP;

class LogVisitor
{
    public function handle($request, Closure $next)
    {
        try {

            // Get IP address
            $ip = $request->ip();

            // Get location data
            $location = GeoIP::getLocation($ip);
            $country = $location->getAttribute('country');
            $state = $location->getAttribute('state');
            $city = $location->getAttribute('city');

            // Get system name and device details
            $agent = new Agent();
            $systemName = $agent->platform();
            $device = $agent->device();
            $browser = $agent->browser();
            $isMobile = $agent->isMobile();

            // Save to database
            DB::table('visitors')->updateOrInsert(['ip_address' => $ip], [
                'ip_address' => $ip,
                'country' => $country,
                'state' => $state,
                'city' => $city,
                'system_name' => $systemName,
                'device' => $device,
                'browser' => $browser,
                'is_mobile' => $isMobile,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } catch (\Throwable $th) {
            
        }

        return $next($request);
    }
}
