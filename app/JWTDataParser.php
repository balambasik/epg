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
        $data = explode('.', (string) $token);
        $data = str_replace(["_", "-"], ["/", "+"], $data[1] ?? "");
        $data = base64_decode($data);
        $data = json_decode($data);

        if (json_last_error()) {
            return null;
        }

        return is_numeric($data->exp) ? $data : null;
    }
}