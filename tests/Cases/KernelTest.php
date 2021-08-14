<?php

namespace Cases;

use App\Kernel;
use App\lib\Http\Response\JsonResponse;
use App\lib\Http\Response\NotFoundResponse;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use Mock\Container;
use Mock\Request;

class KernelTest extends TestCase
{

    private Kernel $kernel;

    /**
     * @throws ReflectionException
     */
    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->kernel = (new Container())->get(Kernel::class);
    }

    /**
     * @throws ReflectionException
     */
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
