<?php

namespace App\Interfaces;

interface CacheDriverInterface
{
    public function setToken($token, $ttl);

    public function checkToken($token);

    public function destroyToken($token);

    public function clearExpiredTokens();
}