<?php

namespace App;

class JWTDataParser
{
    /**
     * @param $token
     * @return mixed|null
     */
    public static function parse($token)
    {
        try {
            $data = json_decode(base64_decode(str_replace('_', '/', str_replace('-', '+', explode('.', (string) $token)[1]))));
            return is_numeric($data->exp) ? $data : null;
        } catch (\Exception $e) {
            return null;
        }
    }
}