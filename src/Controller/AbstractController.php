<?php


namespace App\Controller;


use App\lib\Http\Response\Response;
use App\lib\Http\Response\RedirectResponse;

abstract class AbstractController implements IController
{
    public function redirect(string $url): RedirectResponse
    {
        return new RedirectResponse($url);
    }

    public function render(
        string $body = '',
        array $params = []
    ): Response {
        return new Response($body);
    }
}