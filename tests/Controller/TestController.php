<?php

namespace Test\Controller;

use App\Controller\AbstractController;
use App\Controller\IController;
use App\lib\Http\Response\Response;
use App\lib\Http\Routing\Route;

class TestController extends AbstractController implements IController
{
    #[Route('/test')]
    public function test1(): Response
    {
        return $this->render('test');
    }
}