<?php

namespace Controller;

use App\Controller\AbstractController;
use App\lib\Http\Response\Response;
use App\lib\Http\Routing\Route;

#[Route('/hard')]
class HardController extends AbstractController
{
    #[Route('/{test_param}/test')]
    public function hard(int $test_param = 0): Response
    {
        return $this->json([$test_param]);
    }
}