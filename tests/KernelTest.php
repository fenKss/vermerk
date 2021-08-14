<?php

namespace Test;

use App\Kernel;
use App\lib\Http\Response\JsonResponse;
use App\lib\Http\Response\NotFoundResponse;
use PHPUnit\Framework\TestCase;
use Test\Mock\Container;
use Test\Mock\Request;

class KernelTest extends TestCase
{

    private Kernel $kernel;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->kernel = (new Container())->get(Kernel::class);
    }

    public function testDispatch()
    {
        $request = (new Request())->setUri('/hard/321/test');
        $response = $this->kernel->dispatch($request);
        $this->assertInstanceOf(JsonResponse::class, $response);

        $request = (new Request())->setUri('/notExist');
        $response = $this->kernel->dispatch($request);
        $this->assertInstanceOf(NotFoundResponse::class, $response);
    }
}