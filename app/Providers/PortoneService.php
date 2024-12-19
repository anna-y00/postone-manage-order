<?php

namespace App\Providers;

use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class PortoneService
{
    private $apiBaseUrl;
    private $apiKey;
    private $apiSecret;

    public function __construct()
    {
        $this->apiBaseUrl = 'https://api.iamport.kr';
        $this->apiKey = "1180572031388574"; // REST API Key
        $this->apiSecret = "OgJljopgNZtzsOVNKnnnUBOCXMvMOpO1xYoVH7FY2pzAwTiPRyQJ0SA4NGBC9ELSOHpGR0yBCw6V4NMA"; // REST API SECRET
    }

    /**
     * 인증
     */
    private function authenticate()
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $this->apiBaseUrl."/users/getToken",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode([
                'imp_key' => $this->apiKey,
                'imp_secret' => $this->apiSecret
            ]),
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json"
            ],
        ]);

        $response = curl_exec($curl);
        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
            curl_close($curl);
            throw new Exception("cURL Error: $error_msg");
        }

        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $res = json_decode($response, true);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            \Log::info([$err]);
        } else {
            return $res['response']['access_token'];
        }


    }

    /**
     * 결제 조회
     */
    public function getPaymentDetails($impUid)
    {
        $token = $this->authenticate();
        if (!$token) {
            return null;
        }

        $client = new Client();
        try {
            $response = $client->request('GET', "{$this->apiBaseUrl}/payments/find/{$impUid}/", [
                'body' => '{}',
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => "Bearer {$token}", // 토큰 추가
                ],
            ]);
            $data = json_decode($response->getBody(), true);
            if ($response->getStatusCode()) {
                return $data;
            }
        }catch (RequestException $e) {
            $responseBody = $e->getResponse()->getBody()->getContents();
            $responseJson = json_decode($responseBody, true);

            if (isset($responseJson['message'])) {
                return $responseJson['message'];
            } else {
                \Log::info('Error');
            }

        }
    }

    /**
     * 결제 취소
     */
    public function cancelPayment($impUid)
    {
        $token = $this->authenticate();
        if (!$token) {
            return null;
        }

        $client = new Client();
        try {
            $response = $client->request('POST', "{$this->apiBaseUrl}/payments/cancel", [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => "Bearer {$token}",
                ],
                'json' => [
                    'merchant_uid' => $impUid,
                ],
            ]);
            $data = json_decode($response->getBody(), true);
            if (isset($data['response']['cancel_reason'])) {
                return $data;
            }
        }catch (RequestException $e) {
            \Log::info([$e]);
            return null;
        }
        return $response->json();
    }
}
