<?php

namespace App\lib\Http\Routing;

use App\lib\Di\Container;
use App\lib\Http\Request;
use App\lib\Http\Response\NotFoundResponse;
use App\lib\Http\Response\Response;
use ReflectionException;

class Router
{
    private ControllerLoader $controllerLoader;
    private Container        $container;

    public function __construct(Container $container, ControllerLoader $controllerLoader)
    {
        $this->controllerLoader = $controllerLoader;
        $this->container        = $container;
    }

    /**
     * @throws ReflectionException
     */
    public function dispatch(Request $request): Response
    {
        $route = $this->getRoute($request);
        if (!$route) {
            return new NotFoundResponse();
        }
        $controller = $this->container->get($route->getController());
        return $controller->{$route->getMethod()}(...$route->getParams());

    }

    /**
     * @return array<Route>
     * @throws ReflectionException
     */
    private function getRoutes(): array
    {
        $controllers = $this->controllerLoader->getControllers();
        $routes      = [];
        foreach ($controllers as $controller) {
            $methods       = $controller->getMethods();
            $controllerUrl = $this->getControllerUrl($controller);
            foreach ($methods as $method) {
                foreach ($method->getAttributes() as $attribute) {
                    if ($attribute->getName() == Route::class) {
                        try {
                            $route = new Route(...$attribute->getArguments());
                            $route->setController($controller->getName())->setMethod($method->getName())->setUrl($controllerUrl . '/' . $route->getUrl());
                            $routes[] = $route;
                        } catch (\Throwable) {
                        }
                    }
                }
            }
        }
        return $routes;
    }

    /**
     * @param string $url
     * @param Route  $route
     *
     * @return bool
     */
    private function testUrl(string $url, Route $route): bool
    {
        $routeUrl = $route->getUrl();

        if ($routeUrl == $url) {
            return true;
        }
        $urlPath       = explode('/', $url);
        $routeUrlPath  = explode('/', $routeUrl);
        $is_lvl_equals = count($urlPath) === count($routeUrlPath);
        if (!$is_lvl_equals) {
            return false;
        }
        for ($i = 0; $i < count($urlPath); $i++) {
            $routeUrlChunk = $routeUrlPath[$i];
            $urlChunk      = $urlPath[$i];
            /**
             * Chunk считается параметром, если он обёрнут в {}
             */
            $isParameterChunk = $routeUrlChunk[0] == '{' && $routeUrlChunk[strlen($routeUrlChunk) - 1] == "}";
            if (!$isParameterChunk && $routeUrlChunk != $urlChunk) {
                return false;
            }
        }
        return true;
    }

    /**
     * @throws ReflectionException
     */
    private function getRoute(Request $request): ?Route
    {
        $routes = $this->getRoutes();
        foreach ($routes as $route) {
            if ($this->testRequest($request, $route)) {
                return $this->addParamsToRoute($route, $request);
            }
        }
        return null;
    }

    /**
     * @param Request $request
     * @param Route   $route
     *
     * @return bool
     */
    private function testRequest(Request $request, Route $route): bool
    {
        return $this->testRequestMethod($request, $route) && $this->testUrl(trim($request->getUrl(), '/'), $route);
    }

    /**
     * @param Request $request
     * @param Route   $route
     *
     * @return bool
     */
    private function testRequestMethod(Request $request, Route $route): bool
    {
        $allowedRequestMethods = $route->getRequestMethods();
        /**
         * Если у рута не указаны методы которые он принимает - он принимает все
         */
        if (!count($allowedRequestMethods)) {
            return true;
        }
        return in_array($request->getMethod(), $allowedRequestMethods);
    }

    /**
     * Добавляет параметры из урла в роут
     *
     * @param Route   $route
     * @param Request $request
     *
     * @return Route
     * @throws ReflectionException
     */
    private function addParamsToRoute(Route $route, Request $request): Route
    {
        $url                   = trim($request->getUrl(), '/');
        $urlPath               = explode('/', $url);
        $routeUrlPath          = explode('/', $route->getUrl());
        $routeReflectionParams = ((new \ReflectionClass($route->getController()))->getMethod($route->getMethod())->getParameters());
        $routeParams           = [];
        foreach ($routeReflectionParams as $routeParam) {
            $routeParams[$routeParam->getName()] = null;
        }

        foreach ($routeUrlPath as $position => $urlChunk) {
            $isParameterChunk = $urlChunk[0] == '{' && $urlChunk[strlen($urlChunk) - 1] == "}";

            if (!$isParameterChunk) {
                continue;
            }
            $chunk = ltrim($urlChunk, "{");
            $chunk = rtrim($chunk, "}");

            foreach ($routeParams as $name => $value) {
                if ($name == $chunk) {
                    $route->setParam($name, $urlPath[$position] ?? null);
                    $routeParams[$name] = $urlPath[$position];
                }
            }

        }
        foreach ($routeParams as $name => $value) {
            if (is_null($value)) {
                throw new \RuntimeException("Can't set route param '\$$name' in {$route->getController()}::{$route->getMethod()}()");
            }
        }
        return $route;
    }

    /**
     * @param \ReflectionClass $reflectionController
     *
     * @return string
     */
    private function getControllerUrl(\ReflectionClass $reflectionController): string
    {
        foreach ($reflectionController->getAttributes() as $attribute) {
            if ($attribute->getName() == Route::class) {
                return (new Route(...$attribute->getArguments()))->getUrl();
            }
        }
        return '';
    }
}