<?php

$credentials = json_decode(file_get_contents(__DIR__.'/config/sheet.json'));

$key = openssl_pkey_get_private($credentials->private_key);
$payload = [
    "iss" => $credentials->client_email,
    "sub" => $credentials->client_email,
    "scope" => "https://www.googleapis.com/auth/spreadsheets https://www.googleapis.com/auth/spreadsheets.readonly",
    "aud" => $credentials->token_uri,
    "iat" => (new \DateTime())->getTimestamp(),
    "exp" => (new \DateTime())->modify('+ 1 hour')->getTimestamp()
];
print_r($payload);
//print_r($token);
$header = [
    "alg" => "RS256",
    "typ" => "JWT",
    "kid" => $credentials->private_key_id
];

$header    = \rtrim(\strtr(\base64_encode(\json_encode($header, \JSON_UNESCAPED_SLASHES)), '+/', '-_'), '=');
$payload    = \rtrim(\strtr(\base64_encode(\json_encode($payload, \JSON_UNESCAPED_SLASHES)), '+/', '-_'), '=');
\openssl_sign($header . '.' . $payload, $signature, $key, \OPENSSL_ALGO_SHA256);
$signature = \rtrim(\strtr(\base64_encode($signature), '+/', '-_'), '=');

$token =  $header . '.' . $payload . '.' . $signature;


$data = [
    'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
    'assertion' => $token,
];

$ch = curl_init($credentials->token_uri);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
if($response === false)
{
    echo 'Curl error: ' . curl_error($ch);
} else {
    $auth_data = json_decode($response, true);
    $data = [
        'values' => [[11,22,33,44]]
    ];
    $authorization = "Authorization: Bearer ".$auth_data['access_token'];
    $ch2 = curl_init('https://sheets.googleapis.com/v4/spreadsheets/1APvvpvUseIDJAvomQHeDvjHhbAjilSZvTR_P1UcwiDs/values/Sheet9!R1C1:append?valueInputOption=RAW&alt=json');
    curl_setopt($ch2, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization ));
    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch2, CURLOPT_POST, true);
    curl_setopt($ch2, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch2, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
    $response2 = curl_exec($ch2);
    if($response2 === false)
    {
        echo 'Curl error: ' . curl_error($ch2);
    } else {
        print_r(json_decode($response2, true));
        print_r('yooohoo');
    }
    curl_close($ch2);
}
curl_close($ch);
