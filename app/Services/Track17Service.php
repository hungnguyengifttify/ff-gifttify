<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;

class Track17Service
{
    private $client;

    function __construct()
    {
        $this->token = Config::get('17track.token');
        $this->client = new \GuzzleHttp\Client();
    }

    // Add tracking number to account to tracking
    function registerTrackingNumber($numberTracking)
    {
        $body[] = array(
            'number' => $numberTracking,
            'auto_detection' => true,
        );

        $resTracking = $this->client->request('POST', Config::get('17track.url_register'), [
            'headers' => ['Content-Type' => 'application/json', 'Accept' => 'application/json', '17token' => $this->token],
            'body' => json_encode($body)
        ]);

        if ($resTracking->getStatusCode() == '200') {
            $body = $resTracking->getBody()->getContents();
            $body = json_decode($body);
            return $body;
        }
        return false;
    }

    // Get infomation tracking
    function getTrackinfo($numberTracking)
    {
        $body[] = array(
            'number' => $numberTracking,
        );
        $resTracking = $this->client->request('POST', Config::get('17track.url_gettrackinfo'), [
            'headers' => ['Content-Type' => 'application/json', 'Accept' => 'application/json', '17token' => $this->token],
            'body' => json_encode($body)
        ]);

        if ($resTracking->getStatusCode() == '200') {
            $body = $resTracking->getBody()->getContents();
            $body = json_decode($body);
            return $body;
        }
        return false;
    }
}
