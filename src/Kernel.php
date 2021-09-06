<?php


namespace App;


use Dotenv\Dotenv;
use App\lib\Config;
use App\lib\Di\Container;
use App\lib\Http\IRequest;
use App\lib\Http\Request;
use App\lib\Http\Response\InternalErrorResponse;
use App\lib\Http\Response\NotFoundResponse;
use App\lib\Http\Response\Response;
use App\lib\Http\Routing\IRouter;
use ReflectionException;

class Kernel
{
    private Container $container;

    public function __construct()
    {
        $this->container = new Container();
    }

    /**
     * @throws ReflectionException
     */
    public function dispatch(Request $request): Response
    {
        $router = $this->container->get(IRouter::class);
        /** @var IRouter $router */
        $route = $router->getRoute($request);
        if (!$route) {
            return new NotFoundResponse();
        }
        $controller = $this->container->get($route->getController());
        return $controller->{$route->getMethod()}(...$route->getParams());

    }

    public function run()
    {
        try {
            /** @var Request $request */
            $request = $this->getContainer()->get(IRequest::class);
            $response = $this->dispatch($request);
        } catch (\Throwable $e) {
            error_log($e);
            $response = new InternalErrorResponse();
        } finally {
            foreach ($response->getHeaders() as $header => $value) {
                header("$header: $value");
            }
            http_response_code($response->getStatusCode());
            echo $response->getBody();
        }
    }

    public function getContainer(): Container
    {
        return $this->container;
    }

}