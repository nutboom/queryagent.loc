<?php

class YiiGcm extends CApplicationComponent {
    public $key;

    const URLGOOGLE = 'https://android.googleapis.com/gcm/send';

    public function init() {
        if (!$this->key) {
            throw new CException('Api key cannot be empty');
        }

        parent::init();
    }


    public function send($tokens, $message, $payloadData = array()) {
        if (!is_array($tokens)) {
            $tokens = array($tokens);
        }

        $headers = array(
            'Content-Type: application/json;charset=UTF-8',
            'Authorization:key=' . $this->key
        );

        $json = array(
            "registration_ids" => $tokens,
            "data" => array(
                "message" => $message,
            ),
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::URLGOOGLE);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($json));

        $response = curl_exec($ch); 
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); 

        if (curl_errno($ch)) {  
            return false;
        }  
        if ($httpCode != 200) {  
            return false;  
        }  
        curl_close($ch);  

        return $response;
    }
} 