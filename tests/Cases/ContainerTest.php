<?php

namespace Cases;

use App\lib\Config\Config;
use App\lib\Di\Container;
use App\lib\Http\Routing\IRouter;
use App\lib\Http\Routing\Router;
use PHPUnit\Framework\TestCase;
use ReflectionException;

final class ContainerTest extends TestCase
{
    private Container $container;

    public function setUp(): void
    {
        $this->container = new Container();
    }

    public function testGet()
    {
        foreach ([Router::class, Config::class, Container::class] as $class) {
            $instance = $this->container->get($class);
            $this->assertInstanceOf($class, $instance);
        }

    }

    /**
     * @throws ReflectionException
     */
    public function testInterfaces()
    {
        $interfaceMapping = [
            IRouter::class => Router::class
        ];
        $container = new Container([], $interfaceMapping);
        $router = $container->get(IRouter::class);
        $this->assertInstanceOf(Router::class, $router);

    }

    /**
     * @throws ReflectionException
     */
    public function testSingletons()
    {
        $singletons = [
           Router::class
        ];
        $container = new Container($singletons);
        $router1 = $container->get(Router::class);
        $router2 = $container->get(Router::class);
        $this->assertTrue($router1 === $router2);
    }

    public function testInvalidClass()
    {
        $this->expectException(ReflectionException::class);
        $this->container->get('Invalid Class');
    }
}