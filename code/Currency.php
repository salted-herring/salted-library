<?php
/**
 * @file Currency
 *
 * Generic Currency functions
 * */
namespace SaltedHerring;
use GuzzleHttp\Client;
class Currency
{
    public static function exchange($amount, $from = 'NZD', $to = 'CNY', $date = 'latest')
    {
        $client = new Client([
            'base_uri' => 'http://api.fixer.io/'
        ]);

        $query = array(
            'base'      =>  $from,
            'symbols'   =>  $to
        );

        $response = $client->request(
            'GET',
            $date,
            array(
                'query' => $query
            )
        );

        $data   =   json_decode($response->getBody());
        $amount =   $data->rates->$to;

        return number_format($amount, 2, '.', ',');
    }
}
