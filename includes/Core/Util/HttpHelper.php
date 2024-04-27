<?php

namespace BitCode\FI\Core\Util;

final class HttpHelper
{
    public static $responseCode = null;

    public static function post($url, $data, $headers = null, $options = null)
    {
        return static::request($url, 'POST', $data, $headers, $options);
    }

    public static function get($url, $data, $headers = null, $options = null)
    {
        return static::request($url, 'GET', $data, $headers, $options);
    }

    public static function request($url, $type, $data, $headers = null, $options = null)
    {
        static::$responseCode = null;
        $defaultOptions = [
            'method'    => $type,
            'headers'   => $headers,
            'body'      => $data,
            "timeout"   => 30
        ];
        $options = wp_parse_args($options, $defaultOptions);
        $requestReponse = wp_remote_request($url, $options);
        if (is_wp_error($requestReponse)) {
            return $requestReponse;
        }
        // $responseCode = wp_remote_retrieve_response_code($requestReponse);
        // if (!\is_null($responseCode) && $responseCode != 200) {
        //     return wp_remote_retrieve_response_message($requestReponse);
        // }
        static::$responseCode   = wp_remote_retrieve_response_code($requestReponse);
        $responseBody           = wp_remote_retrieve_body($requestReponse);
        $jsonData               = json_decode($responseBody);

        return \is_null($jsonData) ? $responseBody : $jsonData;
    }
}