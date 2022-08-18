<?php
namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class RemixApi {
    public $url;

    private $id;
    private $password;
    private $token;

    public $client;

    function __construct() {
        $this->url = Config::get('remix.api_url');
        $this->id = Config::get('remix.id');
        $this->password = Config::get('remix.password');

        $this->client = new \GuzzleHttp\Client();
        $this->login();
    }

    function __destruct() {
        $this->logout();
    }

    function getToken() {
        return $this->token;
    }

    function login () {
        $this->token = Config::get('remix.permanent_token');
        return true;

        $body = array(
            'id' => $this->id,
            'password' => $this->password,
        );
        $resLogin = $this->client->request('POST', "{$this->url}/login", [
            'headers' => ['Content-Type' => 'application/json', 'Accept' => 'application/json'],
            'body' => json_encode($body)
        ]);

        if ($resLogin->getStatusCode() == '200') {
            $body = $resLogin->getBody()->getContents();
            $body = json_decode($body);
            $this->token = $body->token;
            return true;
        }
        return false;
    }

    function logout () {
        return false;
    }

    function request($method, $uri, $query = null, $body = null) {
        try {
            $res = $this->client->request($method, "{$this->url}/$uri", [
                'headers' => [
                    'Content-Type' =>	'application/json',
                    'Authorization' => "Bearer " . $this->getToken()
                ],
                'query' => $query,
                'body' => json_encode($body)
            ]);
            return $res;
        } catch (\Exception $e) {
            dump($e->getMessage());
            return false;
        }

    }
}
