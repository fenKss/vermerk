<?php

namespace Test\Mock;

use App\lib\Http\IRequest;
use App\lib\Http\Routing\IRouter;
use App\lib\Http\Routing\Router;

class Container extends \App\lib\Di\Container
{
    public function __construct()
    {
        $singletons = [
            \App\lib\Config\DotenvConfig::class,
            \App\lib\Config\Config::class
        ];
        $interfaceMapping = [
            IRequest::class => Request::class,
            IRouter::class => Router::class
        ];
        parent::__construct($singletons, $interfaceMapping);
    }
}