<?php use SaltedHerring\RPC as RPC;

class Recaptcha {
    public static function verify($repsonse, $api = null) {
        $api = !empty($api) ? $api : Config::inst()->get('GoogleAPIs','Recaptcha');
        
        $result = RPC::send('https://www.google.com/recaptcha/api/siteverify', array(
            'secret'    =>  $api,
            'response'  =>  $response
        ));

        $result = json_decode($result);

        return $result;
    }
}
