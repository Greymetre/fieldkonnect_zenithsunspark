<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ExotelService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExotelApiController extends Controller
{
    protected $exotel;

    public function __construct(ExotelService $exotel)
    {
        $this->exotel = $exotel;
    }

    /**
     * API to make a call
     */
    public function makeCall(Request $request)
    {
        $request->validate([
            'from' => 'required', // customer number
            'to'   => 'required', // agent number
        ]);

        $response = $this->exotel->makeCall($request->from, $request->to);

        if (isset($response['RestException'])) {
            return response()->json([
                'success' => false,
                'message' => $response['RestException']['Message'],
            ], 400);
        }

        return response()->json([
            'success' => true,
            'call_sid' => $response['Call']['Sid'] ?? null,
            'status'   => $response['Call']['Status'] ?? null,
        ]);
    }

    /**
     * API to get call details
     */
    public function getCallDetails(Request $request)
    {
        $request->validate([
            'call_sid' => 'required',
        ]);

        $details = $this->exotel->getCallDetails($request->call_sid);

        if (!isset($details['Call'])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Call SID or call not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'call_sid' => $details['Call']['Sid'],
            'status' => $details['Call']['Status'],
            'recording_url' => $details['Call']['RecordingUrl'] ?? null,
        ]);
    }

    /**
     * API to download and save recording
     */
    public function getRecording(Request $request)
    {
        $request->validate([
            'call_sid' => 'required',
        ]);

        $details = $this->exotel->getCallDetails($request->call_sid);

        if (!isset($details['Call']) || !isset($details['Call']['RecordingUrl']) || empty($details['Call']['RecordingUrl'])) {
            return response()->json([
                'success' => false,
                'message' => 'Recording not available yet',
            ], 404);
        }

        $recordingUrl = $details['Call']['RecordingUrl'];
        $recording = $this->exotel->getRecording($recordingUrl);

        $fileName = "recordings/{$request->call_sid}.mp3";
        Storage::disk('public')->put($fileName, $recording);

        return response()->json([
            'success' => true,
            'recording_path' => asset("storage/{$fileName}"),
        ]);
    }
}
