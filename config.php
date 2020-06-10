<?php

function config()
{
    return [

        "check_token_url"   => "https://api.prosto.net/auth",
        "auth_cache_driver" => App\AuthCacheFile::class,

        App\AuthCacheFile::class => [
            "storage_path" => dirname(__FILE__) . "/storage/"
        ],
        \App\AuthCacheDB::class  => [
            "db_host"    => "localhost",
            "db_name"    => "auth_cache",
            "db_user"    => "root",
            "db_pass"    => "",
            "tb_name" => "auth"
        ],

        "clear_expired_tokens_chance" => 100, // 0 - 100%

    ];
}


