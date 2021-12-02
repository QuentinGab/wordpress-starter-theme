<?php

namespace App\Controllers\Traits;

use Exception;

/**
 * This trait provide a simple way to verify an reCAPTACHA v3 token
 */
trait VerifyRecaptcha
{
    public function verifyCaptcha($token)
    {

        $url = config('recaptcha.reCAPTCHA_url_verify');
        if (!$url) {
            throw new Exception("Please specify google reCAPTCHA url in config file");
        }

        $args = [
            'body' => [
                'secret' => config('recaptcha.reCAPTCHA_site_secret'),
                'response' => $token
            ]
        ];

        try {
            $response = wp_remote_post($url, $args);

            $response['body'] = json_decode($response['body'], true);
            $body = $response['body'];
            $code = wp_remote_retrieve_response_code($response);

            if (!is_array($body)) {
                dd($body);
                throw new Exception('Echec  de la vérification reCaptcha : ' . json_encode($body), 500);
            }

            if (!($code >= 200 && $code < 300)) {
                throw new Exception('Echec  de la vérification reCaptcha', 500);
            }

            if ($body['success'] and $body['score'] < 0.5) {
                throw new Exception('Nous vous soupçonnons d\'être un robot', 400);
            }
        } catch (Exception $e) {
            throw $e;
        }

        return $response;
    }
}
