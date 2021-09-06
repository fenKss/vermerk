<?php

namespace App\lib\Http\Routing;

use App\lib\Di\Container;
use App\lib\Http\IRequest;
use ReflectionClass;
use ReflectionException;
use RuntimeException;
use Throwable;

class Router implements IRouter
{
    private ControllerLoader $controllerLoader;
    private Container $container;

    public function __construct(ControllerLoader $controllerLoader, Container $container)
    {
        $this->controllerLoader = $controllerLoader;
        $this->container = $container;
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
                        } catch (Throwable) {
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
    public function getRoute(IRequest $request): ?Route
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
     * @param IRequest $request
     * @param Route   $route
     *
     * @return bool
     */
    private function testRequest(IRequest $request, Route $route): bool
    {
        return $this->testRequestMethod($request, $route) && $this->testUrl(trim($request->getUrl(), '/'), $route);
    }

    /**
     * @param IRequest $request
     * @param Route   $route
     *
     * @return bool
     */
    private function testRequestMethod(IRequest $request, Route $route): bool
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
     * @param IRequest $request
     *
     * @return Route
     * @throws ReflectionException
     */
    private function addParamsToRoute(Route $route, IRequest $request): Route
    {
        $this->setRouteParamsFromUrl(trim($request->getUrl(), '/'), $route->getUrl(), $route);
        $this->testAllParametersFilled($route);
        return $route;
    }

    /**
     * @param ReflectionClass $reflectionController
     *
     * @return string
     */
    private function getControllerUrl(ReflectionClass $reflectionController): string
    {
        foreach ($reflectionController->getAttributes() as $attribute) {
            if ($attribute->getName() == Route::class) {
                return (new Route(...$attribute->getArguments()))->getUrl();
            }
        }
        return '';
    }

    /**
     * @param Route $route
     *
     * @throws ReflectionException
     */
    private function testAllParametersFilled(Route $route)
    {
        $routeReflectionParams = ((new ReflectionClass($route->getController()))->getMethod($route->getMethod())->getParameters());
        foreach ($routeReflectionParams as $param) {
            $name  = $param->getName();
            $value = $route->getParam($name);
            if (!$route->isParamExist($name) || (is_null($value) && !$param->allowsNull())) {
                if ($param->isDefaultValueAvailable()) {
                    $route->setParam($name, $param->getDefaultValue());
                }elseif ($param->allowsNull()){
                    $route->setParam($name, null);
                }else{
                    $value = $this->container->get($param->getType()->getName());
                    $route->setParam($name, $value);
//                    throw new RuntimeException("Can't set route param '$$name' in {$route->getController()}::{$route->getMethod()}()");
                }

            }
        }
    }

    private function setRouteParamsFromUrl(
        string $url,
        string $routeUrl,
        Route  $route
    ) {
        $routeUrlPath = explode('/', $routeUrl);
        $urlPath      = explode('/', $url);
        foreach ($routeUrlPath as $position => $urlChunk) {
            $isParameterChunk = ($urlChunk[0] ?? null) == '{' && $urlChunk[strlen($urlChunk) - 1] == "}";

            if (!$isParameterChunk) {
                continue;
            }
            $chunk = ltrim($urlChunk, "{");
            $chunk = rtrim($chunk, "}");

            $route->setParam($chunk, $urlPath[$position] ?? null);
        }
    }
}