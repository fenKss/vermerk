<?php


namespace App\Controller;


use App\lib\Http\Response\Response;
use App\lib\Http\Response\RedirectResponse;

interface IController
{
    public function render(string $body, array $params): Response;

    public function redirect(string $url): RedirectResponse;

}