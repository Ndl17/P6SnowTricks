<?php

namespace App\Service;

class JWTService
{

    //ON GENERE LE TOKEN
    public function generate(array $header, array $payload, string $secret, int $validity = 10800)
    {
        if ($validity > 0) {
            $now = new \DateTimeImmutable ();
            $exp = $now->getTimestamp() + $validity;
            $payload['iat'] = $now->getTimestamp();
            $payload['exp'] = $exp;
        }

        // on encode en base64
        $base64Header = base64_encode(json_encode($header));
        $base64Payload = base64_encode(json_encode($payload));
        //on nettoie les caracteres speciaux
        $base64Header = str_replace(['+', '/', '='], ['-', '_', ''], $base64Header);
        $base64Payload = str_replace(['+', '/', '='], ['-', '_', ''], $base64Payload);

        // on genere la signature
        $secret = base64_encode($secret);
        $signature = hash_hmac('sha256', $base64Header . "." . $base64Payload, $secret, true);
        $base64Signature = base64_encode($signature);
        $base64Signature = str_replace(['+', '/', '='], ['-', '_', ''], $base64Signature);

        // on genere le token
        $jwt = $base64Header . "." . $base64Payload . "." . $base64Signature;

        return $jwt;
    }

    // on verifie que le token est valide
    public function isValid(string $token)
    {
        return preg_match('/^[a-zA-Z0-9\-\_\=]+\.+[a-zA-Z0-9\-\_\=]+\.+[a-zA-Z0-9\-\_\=]+$/', $token) === 1;
    }

// on récupère payload
    public function getPayload(string $token)
    {
        //on démonte le token
        $payload = explode('.', $token)[1];
        // on décode le payload
        $payload = base64_decode($payload);
        $payload = json_decode($payload, true);
        return $payload;

    }

// on récupère le header
    public function getHeader(string $token)
    {
        //on démonte le token
        $header = explode('.', $token)[0];
        // on décode le payload
        $header = base64_decode($header);
        $header = json_decode($header, true);
        return $header;
    }

    public function isExpired(string $token)
    {
        $payload = $this->getPayload($token);
        $now = new \DateTimeImmutable ();
        $exp = $now->getTimestamp();
        return $payload['exp'] < $exp;
    }

//on verifie la signature du token

    public function check(string $token, string $secret)
    {
        // on recupère header et playload
        $header = $this->getHeader($token);
        $payload = $this->getPayload($token);
        //on regénère le token
        $verifToken = $this->generate($header, $payload, $secret, 0);

        return $token === $verifToken;

    }

}
