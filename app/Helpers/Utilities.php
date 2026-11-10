<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class Utilities
{
    private static function base64UrlEncode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function getAccessToken()
    {
        $keyFile = json_decode(
            file_get_contents(storage_path('app/firebase/service-account.json')),
            true
        );

        $header = self::base64UrlEncode(json_encode([
            'alg' => 'RS256',
            'typ' => 'JWT'
        ]));

        $now = time();
        $payload = self::base64UrlEncode(json_encode([
            'iss'   => $keyFile['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud'   => 'https://oauth2.googleapis.com/token',
            'iat'   => $now,
            'exp'   => $now + 3600,
        ]));

        openssl_sign(
            "$header.$payload",
            $signature,
            $keyFile['private_key'],
            'SHA256'
        );

        $jwt = "$header.$payload." . self::base64UrlEncode($signature);

        $response = Http::asForm()->post(
            'https://oauth2.googleapis.com/token',
            [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion'  => $jwt,
            ]
        );

        return $response['access_token'] ?? null;
    }

    public static function sendPush($token, $title, $body, $data = [])
    {
        $accessToken = self::getAccessToken();
        if (!$accessToken) return false;

        $projectId = 'whitepeony-3151e';

        return Http::withToken($accessToken)->post(
            "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send",
            [
                'message' => [
                    'token' => $token,
                    'notification' => [
                        'title' => $title,
                        'body'  => $body,
                    ],
                    'data' => array_map('strval', $data),
                ]
            ]
        )->json();
    }
}