<?php


namespace App;


use App\lib\Config;
use App\lib\Di\Container;
use App\lib\Http\Request;
use App\lib\Http\Routing\Router;

class Kernel
{
    private Container $container;

    public function __construct()
    {
        $singletons = [
            Config\DotenvConfig::class,
            Config\Config::class
        ];
        $this->container = new Container($singletons);
    }

    public function run()
    {
        /** @var Request $request */
        $request = $this->getContainer()->get(Request::class);
        /** @var Router $router */
        $router   = $this->getContainer()->get(Router::class);
        $response = $router->dispatch($request);
        foreach ($response->getHeaders() as $header => $value) {
            header("$header: $value");
        }
        http_response_code($response->getStatusCode());
        echo $response->getBody();
    }

    public function getContainer(): Container
    {
        return $this->container;
    }

}