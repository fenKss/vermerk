<?php

namespace Test;

use App\lib\Http\Routing\Route;
use App\lib\Http\Routing\Router;
use Test\Mock\Container;
use Test\Mock\Request;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    private Router $router;

    public function __construct()
    {
        parent::__construct();
        $this->router = (new Container())->get(Router::class);
    }

    public function testRequest()
    {
        $request = (new Request())->setUri('/test');
        $route = $this->router->getRoute($request);
        $this->assertInstanceOf(Route::class, $route);

        $request = (new Request())->setUri('/teste');
        $route = $this->router->getRoute($request);
        $this->assertNull($route);
    }

    /**
     * @depends testRequest
     */
    public function testHardRequest()
    {
        $request = (new Request())->setUri('/hard/321/test');
        $route = $this->router->getRoute($request);
        $this->assertTrue($route->getParam('test_param') == 321);
        $this->assertTrue($route->getController() === 'Test\Controller\HardController');
    }

}