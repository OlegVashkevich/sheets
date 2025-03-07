<?php

namespace OlegV;

use Exception;

class Sheet {
    private Credentials $credentials;
    private string $access_token = '';
    private string $sheet;
    /**
     * @throws Exception
     */
    public function __construct(private readonly string $spreadsheet_id, string $credentials_path) {
        $credentials = file_get_contents($credentials_path);
        if(is_string($credentials)) {
            $data = json_decode($credentials, true);
            $this->credentials = new Credentials($data);
            $this->auth();
        } else {
            throw new Exception('The file with credentials file does not exist.');
        }
    }

    /**
     * @throws Exception
     */
    public function auth(): void
    {
        $token = $this->getAuthToken();
        if($token!=''){
            $data = [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $token,
            ];
            $response = $this->request($this->credentials->token_uri, http_build_query($data));
            $array = json_decode($response, true);
            if(is_array($array) && isset($array['access_token']) && is_string($array['access_token'])){
                $this->access_token = $array['access_token'];
            }
        } else {
            throw new Exception('Problem with creating token.');
        }
    }
    public function setSheet(string $sheet): static
    {
        $this->sheet = $sheet;
        return $this;
    }

    /**
     * @param  array<int, array<int,(int|float|string)>>  $values
     * @param  string  $range
     * @return void
     * @throws Exception
     */
    public function append(array $values, string $range = ''):void
    {
        $authorization = "Authorization: Bearer ".$this->access_token;
        $range = $range ? "!$range" : '';
        $body =  json_encode([
            'values' => $values
        ]);
        $uri = 'https://sheets.googleapis.com/v4/spreadsheets/'.$this->spreadsheet_id.'/values/'.$this->sheet.$range.':append?valueInputOption=RAW&alt=json';
        if(is_string($body)) {
            $response = $this->request($uri, $body, $authorization);
            $array = json_decode($response, true);
            var_dump($array);
        }

    }

    //TODO get method
    //TODO get update

    private function getAuthToken(): string
    {
        $header = [
            "alg" => "RS256",
            "typ" => "JWT",
            "kid" => $this->credentials->private_key_id
        ];

        $payload = [
            "iss" => $this->credentials->client_email,
            "sub" => $this->credentials->client_email,
            "scope" => "https://www.googleapis.com/auth/spreadsheets https://www.googleapis.com/auth/spreadsheets.readonly",
            "aud" => $this->credentials->token_uri,
            "iat" => time(),
            "exp" => time()+3600
        ];

        $isError = false;
        $signature = '';

        $header = json_encode($header, JSON_UNESCAPED_SLASHES);
        if(is_string($header)) {
            $header = rtrim(strtr(base64_encode($header), '+/', '-_'), '=');
        } else {
            $isError = true;
        }
        $payload = json_encode($payload, JSON_UNESCAPED_SLASHES);
        if(is_string($payload)) {
            $payload = rtrim(strtr(base64_encode($payload), '+/', '-_'), '=');
        } else {
            $isError = true;
        }
        $key = openssl_pkey_get_private($this->credentials->private_key);
        if(!is_bool($key)) {
            $isOK = openssl_sign($header.'.'.$payload, $signature, $key, OPENSSL_ALGO_SHA256);
            if($isOK && is_string($signature)) {
                $signature = base64_encode($signature);
                $signature = rtrim(strtr($signature, '+/', '-_'), '=');
            } else {
                $isError = true;
            }
        } else {
            $isError = true;
        }

        if(!$isError && is_string($header) && is_string($payload) && is_string($signature)) {
            return  $header . '.' . $payload . '.' . $signature;
        }

        return '';
    }
    /**
     * @param  string  $uri
     * @param  string  $post
     * @param  string  $authorization
     * @return string
     * @throws Exception
     */
    private function request(string $uri, string $post, string $authorization = ''):string
    {
        $ch = curl_init($uri);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        if($authorization != '') {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization ));
        }
        $response = curl_exec($ch);
        if($response === false)
        {
            throw new Exception(curl_error($ch));
        } elseif(is_string($response)) {
            return $response;
        }
        return '';
    }
}