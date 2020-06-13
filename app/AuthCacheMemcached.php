<?php

namespace App;

use App\Interfaces\CacheDriverInterface;
use App\Exceptions\CacheDriverException;

class AuthCacheMemcached implements CacheDriverInterface
{
    private $memcached;

    public function __construct()
    {
        $config = config()[__CLASS__];

        try {
            $this->memcached = new \Memcached();
            $this->memcached->addServer($config["server"], $config["port"]);
        } catch (\Throwable $e) {
            throw new CacheDriverException($e->getMessage());
        }

        $this->clearExpiredTokens();
    }

    /**
     * @param $token
     * @param $ttl
     */
    public function setToken($token, $ttl)
    {
        $this->memcached->set(self::prepKey($token), $ttl, $ttl);
    }

    /**
     * @param $token
     * @return bool
     */
    public function checkToken($token)
    {
        return $this->memcached->get(self::prepKey($token)) > time();
    }

    /**
     * @param $token
     */
    public function destroyToken($token)
    {
        $this->memcached->delete(self::prepKey($token));
    }

    /**
     * Delete all expired tokens
     */
    public function clearExpiredTokens()
    {
        // It is not necessary to clear expired tokens; the token expiration time is set when writing
    }

    /**
     * @param $token
     * @return string
     */
    private static function prepKey($token)
    {
        return md5($token);
    }
}