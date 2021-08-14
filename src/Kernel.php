<?php


namespace App;


use App\lib\Config;
use App\lib\Di\Container;
use App\lib\Http\IRequest;
use App\lib\Http\Request;
use App\lib\Http\Response\NotFoundResponse;
use App\lib\Http\Response\Response;
use App\lib\Http\Routing\IRouter;
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
        $interfaceMapping = [
            IRequest::class => Request::class,
            IRouter::class => Router::class
        ];
        $this->container = new Container($singletons, $interfaceMapping);
    }

    public function dispatch(Request $request): Response
    {
        try {
            $router = $this->container->get(IRouter::class);
            $route = $router->getRoute($request) ?? throw new \Exception();
            $controller = $this->container->get($route->getController());
            return $controller->{$route->getMethod()}(...$route->getParams());
        } catch (\Exception) {
            return new NotFoundResponse();
        }
    }

    public function run()
    {
        /** @var Request $request */
        $request = $this->getContainer()->get(IRequest::class);
        $response = $this->dispatch($request);
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