<?php

namespace Kreets\SuzoriSms;

class SuzoriSms
{
    const SUZORI_API_URL = 'https://sozuri.net/api/v1/messaging';
    const SUZORI_CHANNEL_SMS = "sms";
    const SUZORI_TYPE_TRANSACTIONAL = "transactional";
    const SUZORI_TYPE_PROMOTIONAL = "promotional";

    private $query = [];

    private function getApiKey()
    {
        $key = config('suzorisms.key');
        if(null == $key){
            throw new \Exception("API KEY MISSING");
        }
        return $key;
    }

    private function getSender(){
        $sender = config('suzorisms.sender');
        if(null == $sender){
            throw new \Exception("SENDER DATA MISSING");
        }
        return $sender;
    }

    private function getProjectname(){
        $projectname = config('suzorisms.project');
        if(null == $projectname){
            throw new \Exception("PROJECT DATA MISSING");
        }
        return $projectname;
    }

    public function send($to, $message, $from = null, $channel = self::SUZORI_CHANNEL_SMS, $type = self::SUZORI_TYPE_TRANSACTIONAL)
    {
        try{
            $this->query['from'] = $from ?? self::getSender();
            $this->query['to'] = str_replace('+','',$to);
            $this->query['message'] = trim($message);
            $this->query['channel'] = $channel;
            $this->query['project'] = self::getProjectname();
            $this->query['type'] = $type;
            return $this->executeRequest();
        }catch (\Exception $e){
            $this->saveToLog("ERROR:".$e->getMessage());
            return false;
        }
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
        curl_close($curl);
        if($response_code == 401){
            throw new \Exception("Unauthorized", 401);
        }

        $this->saveToLog("RESPONSE:".print_r($result,true));
        return ($result === false) ? false : $result;
    }

    private function saveToLog($data = null){
        $data = $data ?? 'json query:'.PHP_EOL.json_encode($this->query).PHP_EOL;
        $logfile = storage_path(config('suzorisms.log'));
        file_put_contents($logfile, $data, FILE_APPEND);
    }
}