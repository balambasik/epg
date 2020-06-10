<?php

namespace App\Controllers;

use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Response\JsonResponse;

class EPGController
{
    public function show(ServerRequest $request)
    {

        return new JsonResponse([
            1,2,3
        ]);
    }
}