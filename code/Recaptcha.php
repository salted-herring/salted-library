<?php
namespace SaltedHerring;
class Recaptcha {
    public static function verify($response, $api = null) {
        $api = !empty($api) ? $api : \Config::inst()->get('GoogleAPIs','Recaptcha');
        $result = RPC::send('https://www.google.com/recaptcha/api/siteverify', array(
            'secret'    =>  $api,
            'response'  =>  $response
        ));

        $result = json_decode($result);

        return $result;
    }
}
