<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 02.02.15
 * Time: 16:03
 */
class BitlyApiComponent extends CApplicationComponent{
    public $access_token;

    public function init(){
        parent::init();
    }

    public function getShorten($url){
        $request = 'https://api-ssl.bitly.com/v3/shorten?'
            .'access_token='.$this->access_token
            .'&longUrl='.urlencode($url);

        //отправляем запрос
        $response = file_get_contents($request);
        $res = json_decode($response, true);

        return $res;
    }

    public function getClicks($url){
        $shorten_url = $this->getShorten($url);
        $request = 'https://api-ssl.bitly.com/v3/link/clicks?'
            .'access_token='.$this->access_token
            .'&link='.urlencode($shorten_url['data']['url']).'&format=txt';

        //отправляем запрос
        $response = file_get_contents($request);
        return $response;
    }
}