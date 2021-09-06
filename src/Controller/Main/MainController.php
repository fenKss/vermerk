<?php


namespace App\Controller\Main;


use App\lib\Config\Config;
use App\Controller\AbstractController;
use App\lib\Http\Response\Response;
use App\lib\Http\Routing\Route;

#[Route('/')]
class MainController extends AbstractController
{
    #[Route(
        '/test/{test_id}/123',
        requestMethods: ['get']
    )]
    public function main(?int $test_id, int $some_id = 0): Response
    {
        return $this->render("You suck $test_id");
    }

    #[Route(
        '/',
        requestMethods: ['get']
    )]
    public function index( Config $config, ?int $test_id, int $some_id = 0): Response
    {
        $a =$config->get('test');
        return $this->render("You suck $test_id");
    }
}