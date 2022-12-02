<?php

namespace Kreets\SuzoriSms;

class SuzoriSms
{
    const SUZORI_API_URL = 'https://sozuri.net/api/v1/messaging';
    const SUZORI_TYPE_TRANSACTIONAL = "transactional";
    const SUZORI_TYPE_PROMOTIONAL = "promotional";

    private $query = [];

    private function getApiKey()
    {
        return config('kreets.suzorisms.key');
    }

    private function getSender(){
        return config('kreets.suzorisms.sender');
    }

    private function getProjectname(){
        return config('kreets.suzorisms.project');
    }

    public function send($to, $message, $from = null, $channel = "sms", $type = self::SUZORI_TYPE_TRANSACTIONAL)
    {
        $this->query['from'] = $from ?? self::getSender();
        $this->query['to'] = str_replace('+','',$to);
        $this->query['message'] = "";
        $this->query['channel'] = "";
        $this->query['project'] = "";
        $this->query['type'] = "";
    }

    public function executeRequest(){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, self::SUZORI_API_URL);

        $headers = array(
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Bearer '.self::getApiKey()
        );

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_ENCODING, "");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($this->query));

        $this->saveToLog();

        $result = curl_exec($curl);
        $response_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $err = curl_error($curl);

        $this->saveToLog("RESPONSE:".print_r($result,true));

        curl_close($curl);
        return ($result === false) ? false : true;
    }

    private function saveToLog($data = null){
        $data = $data ?? 'json query:'.PHP_EOL.json_encode($this->query).PHP_EOL;
        $logfile = storage_path(config('kreets.suzorisms.log'));
        file_put_contents($logfile, $data, FILE_APPEND);
    }
}