<?php

namespace App\Services;

use Carbon\Carbon;
use FacebookAds\Http\Response;
use Illuminate\Support\Facades\Config;

class Track17Service
{
    // List of Error Codes
    // 0	        Success
    // -18010001	Your IP is not in the whitelist. You can set it in the dashboard.
    // -18010002	Invalid security key.
    // -18010003	Internal service error. Please try again later.
    // -18010004	The account is disabled.
    // -18010005	Unauthorized access.
    // -18010010	Please provide the data {0}.
    // -18010011	The value of data {0} is invalid.
    // -18010012	The format of the data {0} is invalid.
    // -18010013	Invalid submitted data.
    // -18010014	Tracking numbers exceed 40 limit.
    // -18010015	The value {0} of the field {1} is invalid.
    // -18010016	Last-mile carrier can only be set for postal services.
    // -18010204	Webhook URL not set, can't push data.
    // -18019901	Tracking number {0} is already registered.
    // -18019902	Tracking number {0} is not registered yet. Please register first.
    // -18019903	The carrier can not be identified.
    // -18019904	Only stopped numbers can be re-tracked.
    // -18019905	Each tracking number can only be re-tracked once.
    // -18019906	Only numbers being tracked can be stopped.
    // -18019907	Tracking amount exceeds your daily limit.
    // -18019908	Your quotas have ran out.
    // -18019909	No tracking info at the moment.
    // -18019910	Carrier Code {0} is incorrect.
    // -18019911	The tracking number of this carrier can not be registered at the moment.
    // -18019801	The tracking number is registered with multiple carriers. Please specify which tracking number you want to change the carrier code for by specifying the carrier_old parameter.
    // -18019802	The parameter carrier_new {0} is incorrect
    // -18019803	The Carrier Code to be changed can not be the same of the current one.
    // -18019804	The Carrier Code to be changed must be specified to carrier_new or final_carrier_new.
    // -18019805	The tracking number {1} for the specified carrier {0} is not registered, or the existing carrier parameter carrier_old is incorrect.
    // -18019806	Carrier can not be changed for stopped numbers. Please retrack the number before changing the carrier.
    // -18019807	The times for changing carrier exceed limit.
    // -18019808	The tracking result has not been returned after the latest registration or modification. Please wait for the tracking result to be returned before changing it.
    // -18019809	The registration information of the carrier with tracking number {0} already exists and cannot be changed to a duplicate registration information.
    // -18019810	Data that meet the update condition are not unique.
    // -18019811	The data need to be changed is not valid.

    private $client;

    function __construct()
    {
        $this->token = Config::get('track17.token_17');
        $this->client = new \GuzzleHttp\Client();
    }

    // Add tracking number to account to tracking
    function registerTrackingNumber($numberTracking)
    {
        $body[] = array(
            'number' => $numberTracking,
            'auto_detection' => true,
        );

        $resTracking = $this->client->request('POST', Config::get('track17.url_register'), [
            'headers' => ['Content-Type' => 'application/json', 'Accept' => 'application/json', '17token' => $this->token],
            'body' => json_encode($body)
        ]);

        if ($resTracking->getStatusCode() == '200') {
            $body = $resTracking->getBody()->getContents();
            $body = json_decode($body, true);

            if ($body['code'] == 0 && isset($body['data']['rejected'][0]) && count($body['data']['rejected'][0])) {
                return $body['data']['rejected'][0];
            }
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
        $resTracking = $this->client->request('POST', Config::get('track17.url_gettrackinfo'), [
            'headers' => ['Content-Type' => 'application/json', 'Accept' => 'application/json', '17token' => $this->token],
            'body' => json_encode($body)
        ]);
        if ($resTracking->getStatusCode() == '200') {
            $body = $resTracking->getBody()->getContents();
            $body = json_decode($body, true);
            if ($body['code'] == 0 && count($body['data']['rejected'])) {
                return $body['data']['rejected'][0];
            }
            return $this->handleDataTrack($body['data']);
        }

        return false;
    }

    function handleDataTrack($data)
    {
        $response = [];
        $response['shipper_address']['country'] = $data['accepted'][0]['track_info']['shipping_info']['shipper_address']['country'];
        $response['recipient_address']['country'] = $data['accepted'][0]['track_info']['shipping_info']['recipient_address']['country'];
        $response['status'] = $data['accepted'][0]['track_info']['latest_status']['status'];
        $response['time_metrics'] = $data['accepted'][0]['track_info']['time_metrics'];
        foreach ($data['accepted'][0]['track_info']['tracking']['providers'][0]['events'] as $val) {
            $response['tracking'][] = [
                'time' =>  $val['time_utc'],
                'content' =>  $val['location'] . ' - ' . $val['description'],
                'stage' =>  $val['stage']
            ];
        }
        return $response;
    }
}
