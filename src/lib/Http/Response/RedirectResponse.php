<?php


namespace App\lib\Http\Response;


class RedirectResponse extends Response
{
    public function __construct(string $url)
    {
        parent::__construct('', StatusCode::MOVED_TEMPORARILY, [
            'Location' => $url
        ]);
    }
}