<?php

namespace App\Middleware;

use App\Auth;
use MiladRahimi\PhpRouter\Middleware;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;

class AuthMiddleware implements Middleware
{

    public function handle(ServerRequestInterface $request, \Closure $next)
    {
        $auth = new Auth();
        $auth->setCheckTokenURL(config()["check_token_url"]);

        if ($authCacheDriver = config()["auth_cache_driver"]) {
            $auth->setCacheDriver(new $authCacheDriver);
        }

        $token = $request->getHeaderLine('Authorization');

        if ($auth->check(str_replace("Bearer ", "", $token))) {
            return $next($request);
        }

        return new JsonResponse(['error' => 'Unauthorized!'], 401);
    }
}