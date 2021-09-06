<?php


use App\lib\Http\Routing\Router;
use App\lib\Http\Routing\IRouter;

return [
    'singletons' => [
        Router::class
    ],
    'interfaceMapping' => [
        IRouter::class => Router::class
    ],
];