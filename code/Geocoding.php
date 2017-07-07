<?php

namespace SaltedHerring;

class Geocoding
{
    public static function LocToCoord($address, $api = '')
    {
        $client = new Client([
            'base_uri' => 'https://maps.googleapis.com/maps/api/geocode/'
        ]);

        $query = array(
            'address'   =>  $address
        );

        if (!empty($api)) {
            $query['key'] = $api;
        }

        $response = $client->request(
            'GET',
            'json',
            array(
                'query' => $query
            )
        );

        return json_decode($response->getBody());
    }

    public static function CoordToLoc($lat, $lng, $api = '')
    {
        $client = new Client([
            'base_uri' => 'https://maps.googleapis.com/maps/api/geocode/'
        ]);

        $query = array(
            'latlng'   =>  $lat . ',' . $lng
        );

        if (!empty($api)) {
            $query['key'] = $api;
        }

        $response = $client->request(
            'GET',
            'json',
            array(
                'query' => $query
            )
        );

        return json_decode($response->getBody());
    }
}
