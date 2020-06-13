<?php

function config()
{
    return [

        "check_token_url"   => "https://api.prosto.net/auth",
        "auth_cache_driver" => \App\AuthCacheFile::class, // auth cache driver or null

        App\AuthCacheFile::class       => [
            "storage_path" => dirname(__FILE__, 2) . "/storage/"
        ],

        \App\AuthCacheDB::class        => [
            "db_host" => "localhost",
            "db_name" => "auth_cache",
            "db_user" => "root",
            "db_pass" => "",
            "tb_name" => "auth"
        ],

        \App\AuthCacheMemcached::class => [
            "server" => "127.0.0.1",
            "port"   => "11211",
        ],

        "clear_expired_tokens_chance" => 1, // 0 - 100%
    ];
}


