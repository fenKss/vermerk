<?php

use App\lib\Config\Config;
use App\lib\Di\Container;
use App\lib\Http\Routing\Router;
use PHPUnit\Framework\TestCase;

final class ContainerTest extends TestCase
{

    private Container $container;

    public function setUp(): void
   {
       $this->container = new Container();
   }

    public function testGet()
    {
        foreach([Router::class, Config::class, Container::class] as $class){
            $instance =$this->container->get($class);
            $this->assertInstanceOf($class, $instance);
        }

    }

    public function testInvalidClass()
    {
        $this->expectException(ReflectionException::class);
        $this->container->get('Invalid Class');
    }
}