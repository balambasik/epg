<?php

namespace App;

use App\Interfaces\CacheDriverInterface;
use GuzzleHttp\Client;


class Auth
{
    protected $cacheDriver;
    protected $checkTokenURL;

    /**
     * @param CacheDriverInterface $cacheDriver
     * @return $this
     */
    public function setCacheDriver(CacheDriverInterface $cacheDriver)
    {
        $this->cacheDriver = $cacheDriver;
        return $this;
    }

    /**
     * @param $checkTokenURL
     * @return $this
     */
    public function setCheckTokenURL($checkTokenURL)
    {
        $this->checkTokenURL = $checkTokenURL;
        return $this;
    }

    /**
     * @param $token
     * @return bool
     */
    public function check($token)
    {
        $tokenData = JWTDataParser::parse($token);

        // invalid token
        if (is_null($tokenData)) {
            return false;
        }

        // check token on cache
        if ($this->cacheDriver) {

            if ($this->cacheDriver->checkToken($token)) {
                return true;
            } else {
                $this->cacheDriver->destroyToken($token);
            }
        }

        // check token on server
        if ($this->serverCheck($token)) {

            if ($this->cacheDriver) {
                $this->cacheDriver->setToken($token, $tokenData->exp);
            }

            return true;
        }

        return false;
    }


    /**
     * @param $token
     * @return bool
     */
    public function serverCheck($token)
    {
        $client = new Client();

        $resp = $client->get($this->checkTokenURL, [
            'headers'     => [
                'Authorization' => "Bearer " . $token,
            ],
            'debug'       => false,
            'http_errors' => false
        ]);

        return $resp->getStatusCode() == 200;
    }

}