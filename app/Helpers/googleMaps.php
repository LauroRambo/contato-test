<?php

use Carbon\Carbon;


//integração com google maps
function getCoordinatesFromAddress($cep) {
    $apiKey = env('GOOGLE_MAPS_API_KEY');
    $url = "https://maps.googleapis.com/maps/api/geocode/json";

    $response = Http::get($url, [
        'address' => $cep,
        'key' => $apiKey,
    ]);

    if ($response->successful()) {
        $data = $response->json();

        if (isset($data['results'][0]['geometry']['location'])) {
            return $data['results'][0]['geometry']['location'];
        }
    }

    return null;
}