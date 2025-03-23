<?php

namespace App\Services\Feeder;

use App\Models\Configuration;
use GuzzleHttp\Client;

class FeederAPI {
    // url Feeder Dikti
    private $url;
    // Username Feeder Dikti
    private $username;
    // Password
    private $password;
    //data
    private $act, $offset, $limit, $order, $filter;


    function __construct($act, $offset, $limit, $order, $filter = null) {

        $config = Configuration::first();

        $this->url = $config->url;
        $this->username = $config->username;
        $this->password = $config->password;
        $this->act = $act;
        $this->offset = $offset;
        $this->limit = $limit;
        $this->order = $order;
        $this->filter = $filter;

    }

    public function runWS()
    {

        $result = $this->get_token();

        if($result['error_code'] == 0) {
            $token = $result['data']['token'];
            $params = [
                "token" => $token,
                "act"   => $this->act,
                "offset" => $this->offset,
                "order" => $this->order,
                "limit" => $this->limit,
                "filter" => $this->filter,
            ];

            $result = $this->service_native($params, $this->url);

        }

        return $result;
    }

    private function get_token()
    {
        $client = new Client();
        $params = [
            "act" => "GetToken",
            "username" => $this->username,
            "password" => $this->password,
        ];

        $req = $client->post( $this->url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'body' => json_encode($params)
        ]);

        $response = $req->getBody();
        $result = json_decode($response,true);

        return $result;
    }

    private function service_native($data,$url,$type='POST') {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_POST, 1);

        $headers = array();
        // $headers[] = 'Authorization: Bearer '.$this->get_token();
        $headers[] = 'Content-Type: application/json';

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
        $data = json_encode($data);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        //curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($ch);

        //print_r($data);
        curl_close($ch);

        return json_decode($result, true);
    }
}
