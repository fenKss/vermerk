<?php


namespace App\Controller\Main;


use App\Controller\AbstractController;
use App\lib\Http\Response\Response;

class MainController extends AbstractController
{
    /**
     * @Route('/main')
     */
    public function main(): Response
    {
        return $this->render(123);
    }
}