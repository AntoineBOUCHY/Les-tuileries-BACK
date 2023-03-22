<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpClient\Exception\ClientException;

class Paypal
{
    private $paypalClientId;
    private $paypalClientSecret;
    private $client;

    public function __construct($paypalClientId, $paypalClientSecret, HttpClientInterface $client)
    {
        $this->paypalClientId = $paypalClientId;
        $this->paypalClientSecret = $paypalClientSecret;
        $this->client = $client;
    }

    public function getClientId()
    {
        return $this->paypalClientId;
    }

    public function getAccessToken(): string
    {
        $response = $this->client->request(
            'POST',
            'https://api-m.sandbox.paypal.com/v1/oauth2/token',
            [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded'
                ],
                'auth_basic' => [
                    $this->paypalClientId,
                    $this->paypalClientSecret
                ],
                'body' => [
                    'grant_type' => 'client_credentials'
                ]
            ]
        );

        return $response->toArray()['access_token'];
    }

    public function getCapture($authorizationId)
    {
        try{
            $response = $this->client->request(
                'POST',
                'https://api-m.sandbox.paypal.com/v2/payments/authorizations/' . $authorizationId . '/capture',
                [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Prefer' => 'return=representation'
                    ],
                    'auth_bearer' => $this->getAccessToken()
                ]
            );

        } catch(ClientException $error) {

            return $error;
        }

        return $response->toArray();
    }
}