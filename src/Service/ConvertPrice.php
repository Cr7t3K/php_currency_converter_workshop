<?php

namespace App\Service;

use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ConvertPrice
{
    public function __construct(
        private HttpClientInterface $client,
        private string $apiKey,
    ) {
    }

    public function checkCode(string $pairCode): bool
    {
        $response = $this->client->request(
            'GET',
            "https://v6.exchangerate-api.com/v6/$this->apiKey/codes"
        );

        $content = $response->toArray();
        $codes = $content['supported_codes'];

        foreach ($codes as $code) {
            if (in_array($pairCode, $code)) {
                return true;
            }
        }

        return false;
    }

    public function convertEur(float $euroPrice, string $pair): float
    {
        if (!$this->checkCode($pair)) {
            throw new Exception('Wrong pair Code');
        }

        $response = $this->client->request(
            'GET',
            "https://v6.exchangerate-api.com/v6/$this->apiKey/pair/EUR/$pair/$euroPrice"
        );

        $content = $response->toArray();
        // $content = ['id' => 521583, 'name' => 'symfony-docs', ...]

        return $content['conversion_result'];
    }
}