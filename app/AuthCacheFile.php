<?php

namespace App;

use App\Interfaces\CacheDriverInterface;
use App\Exceptions\CacheDriverException;

class AuthCacheFile implements CacheDriverInterface
{
    private $storagePath;

    public function __construct()
    {
        $storagePath = config()[__CLASS__]["storage_path"];

        if (!file_exists($storagePath)) {
            throw new CacheDriverException("Directory \"{$storagePath}\" not exists");
        }

        $this->storagePath = $storagePath;

        $this->clearExpiredTokens();
    }

    /**
     * @param $token
     * @param $ttl
     */
    public function setToken($token, $ttl)
    {
        @file_put_contents($this->storagePath . self::prepKey($token), $ttl);
    }

    /**
     * @param $token
     * @return bool
     */
    public function checkToken($token)
    {
        $tokenExpiration = @file_get_contents($this->storagePath . self::prepKey($token));

        if ($tokenExpiration > time()) {
            return true;
        }

        return false;
    }

    /**
     * @param $token
     */
    public function destroyToken($token)
    {
        @unlink($this->storagePath . self::prepKey($token));
    }


    /**
     * Delete all expired tokens
     */
    public function clearExpiredTokens()
    {
        if (config()["clear_expired_tokens_chance"] < rand(1, 100)) {
            return;
        }

        // need redo
        foreach (glob($this->storagePath . "*") as $file) {
            if (is_file($file) && (time() > @file_get_contents($file))) {
                @unlink($file);
            }
        }
    }

    /**
     * @param $token
     * @return string
     */
    protected static function prepKey($token)
    {
        return md5($token);
    }
}