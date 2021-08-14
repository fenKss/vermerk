<?php

namespace App\lib\Http\Routing;


use App\lib\Http\IRequest;

interface IRouter
{
    public function getRoute(IRequest $request): ?Route;
}