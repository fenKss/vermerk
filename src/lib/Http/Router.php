<?php


namespace App\lib\Http;


use App\lib\ControllerService;

class Router
{
    private ControllerService $controllerService;

    public function __construct(ControllerService $controllerService)
    {
        $this->controllerService = $controllerService;
    }


    /**
     * @throws \ReflectionException
     */
    public function dispatch(Request $request)
    {
        $controllers = $this->controllerService->getControllers();
        $uri = $request->getUri();
        dd($controllers, $uri);

    }


}