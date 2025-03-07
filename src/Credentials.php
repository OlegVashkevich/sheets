<?php

namespace OlegV;

class Credentials {
    public string $type;
    public string $private_key_id;
    public string $private_key;
    public string $client_email;
    public string $token_uri;
    public function __construct(mixed $data) {
        if(is_array($data)){
            if (isset($data['type']) && is_string($data['type'])) {
                $this->type = $data['type'];
            }
            if (isset($data['private_key_id']) && is_string($data['private_key_id'])) {
                $this->private_key_id = $data['private_key_id'];
            }
            if (isset($data['private_key']) && is_string($data['private_key'])) {
                $this->private_key = $data['private_key'];
            }
            if (isset($data['client_email']) && is_string($data['client_email'])) {
                $this->client_email = $data['client_email'];
            }
            if (isset($data['token_uri']) && is_string($data['token_uri'])) {
                $this->token_uri = $data['token_uri'];
            }
        }
    }
}