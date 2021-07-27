<?php


namespace App;


use App\lib\Config;
use App\lib\Di\Container;
use App\lib\Http\Request;
use App\lib\Http\Router;

class Kernel
{
    private Container $container;

    public function __construct()
    {
        $this->container = new Container([
            Config\DotenvConfig::class,
            Config\Config::class
        ]);
    }

    public function run()
    {
        /** @var Request $request */
        $request = $this->getContainer()->get(Request::class);
        /** @var Router $router */
        $router = $this->getContainer()->get(Router::class);
        $router->dispatch($request);
    }

    public function getContainer(): Container
    {
        return $this->container;
    }

}