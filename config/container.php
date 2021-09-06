<?php

use App\lib\Http\Request;
use App\lib\Http\IRequest;
use App\lib\Config\Config;
use App\lib\Http\Routing\Router;
use App\lib\Config\DotenvConfig;
use App\lib\Http\Routing\IRouter;

return [
    'singletons' => [
        DotenvConfig::class,
        Config::class,
    ],
    'interfaceMapping' => [
        IRequest::class => Request::class,
        IRouter::class => Router::class,
    ],
];