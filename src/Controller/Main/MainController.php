<?php


namespace App\Controller\Main;


use App\Controller\AbstractController;
use App\lib\Http\Response\NotFoundResponse;
use App\lib\Http\Response\RedirectResponse;
use App\lib\Http\Response\Response;
use App\lib\Http\Routing\Route;

//#[Route('/{id}')]
class MainController extends AbstractController
{

    #[Route('asd/{test_id}/dsa/{some_id}', requestMethods: ['get'] )]
    public function main(int $test_id, string $some_id): Response
    {
        return $this->render(123);
    }
}