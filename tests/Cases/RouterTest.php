<?php

namespace Test\Cases;

use App\lib\Http\Routing\Route;
use App\lib\Http\Routing\Router;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use Test\Mock\Container;
use Test\Mock\Request;

class RouterTest extends TestCase
{
    private Router $router;

    /**
     * @throws ReflectionException
     */
    public function __construct()
    {
        parent::__construct();
        $this->router = (new Container())->get(Router::class);
    }

    /**
     * @throws ReflectionException
     */
    public function testRequest()
    {
        $request = (new Request())->setUri('/test');
        $route = $this->router->getRoute($request);
        $this->assertInstanceOf(Route::class, $route);

        $request = (new Request())->setUri('/test/a');
        $route = $this->router->getRoute($request);
        $this->assertNull($route);
    }

    /**
     * @depends testRequest
     * @throws ReflectionException
     */
    public function testHardRequest()
    {
        $request = (new Request())->setUri('/hard/321/test');
        $route = $this->router->getRoute($request);
        $this->assertTrue($route->getParam('test_param') == 321);
        $this->assertTrue($route->getController() === 'Test\Controller\HardController');
    }

}