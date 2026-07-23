<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ExotelService
{
    protected $sid;          // Account SID (lemosys2)
    protected $apiKey;       // API Key (username)
    protected $apiToken;     // API Token (password)
    protected $virtualNumber; // ExoPhone (CallerId)

    public function __construct()
    {
        $this->sid = config('services.exotel.sid');
        $this->apiKey = config('services.exotel.api_key');
        $this->apiToken = config('services.exotel.token');
        $this->virtualNumber = config('services.exotel.virtual_number');
     
        if (!$this->apiKey || !$this->apiToken) {
            throw new \Exception("Exotel API credentials missing. Check your .env file.");
        }
    }

    /**
     * Initiate a call
     */
    public function makeCall($from, $to)
    {
        $url = "https://api.exotel.com/v1/Accounts/{$this->sid}/Calls/connect.json";

        $response = Http::withBasicAuth($this->apiKey, $this->apiToken)
            ->asForm()
            ->post($url, [
                'From'     => $from,               // Customer number
                'To'       => $to,                 // Agent number
                'CallerId' => $this->virtualNumber, // ExoPhone (assigned by Exotel)
                'CallType' => 'trans',
            ]);
        return $response->json();
    }

    /**
     * Get Call Details by Call SID
     */
    public function getCallDetails($callSid)
    {
        $url = "https://api.exotel.com/v1/Accounts/{$this->sid}/Calls/{$callSid}.json";

        $response = Http::withBasicAuth($this->apiKey, $this->apiToken)->get($url);

        return $response->json();
    }

    /**
     * Download Call Recording
     */
    public function getRecording($recordingUrl)
    {
        $response = Http::withBasicAuth($this->apiKey, $this->apiToken)->get($recordingUrl);

        return $response->body(); // save as mp3/wav file
    }
}
