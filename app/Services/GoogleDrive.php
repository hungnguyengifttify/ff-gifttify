<?php
namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Google\Client;
use Google\Service\Drive;

class GoogleDrive {

    protected $client;
    public $service;

    function __construct() {
        $client = new Client();
        $client->setApplicationName('Google Drive API');
        $client->setScopes(array(
            "https://www.googleapis.com/auth/drive.appdata",
            "https://www.googleapis.com/auth/drive.metadata.readonly",
            "https://www.googleapis.com/auth/drive.readonly",
            "https://www.googleapis.com/auth/drive.file",
            "https://www.googleapis.com/auth/drive.photos.readonly",
            "https://www.googleapis.com/auth/drive.scripts",
            "https://www.googleapis.com/auth/drive.metadata",
            "https://www.googleapis.com/auth/drive"
        ));
        $config = json_decode(Config::get('google.drive_api.json_config'), true);
        $client->setAuthConfig($config);

        if (Cache::has('service_token')) {
            $client->setAccessToken(Cache::get('service_token'));
        }
        if ($client->isAccessTokenExpired()) {
            $client->fetchAccessTokenWithAssertion();
        }
        Cache::forever('service_token', $client->getAccessToken());

        $this->client = $client;
        $this->service = new Drive($client);
    }
}
