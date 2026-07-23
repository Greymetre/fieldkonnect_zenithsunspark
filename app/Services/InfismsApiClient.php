<?php

namespace App\Services;

use GuzzleHttp\Client;

class InfismsApiClient
{
    protected $httpClient;
    protected $apiKey;

    public function __construct()
    {
        $this->httpClient = new Client(['base_uri' => 'https://api.infisms.in/v3/']);
        $this->apiKey = env('INFISMS_API_KEY');
    }

    public static function sendSMS($recipient, $message)
    {
        
        $response = $this->httpClient->post('sms/send', [
            'json' => [
                'apiKey' => $this->apiKey,
                'recipient' => $recipient,
                'message' => $message,
            ]
        ]);

        return $response->getBody()->getContents();
    }

    // Add other methods for interacting with Infisms API
}

