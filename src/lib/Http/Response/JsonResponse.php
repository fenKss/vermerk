<?php

namespace App\lib\Http\Response;

class JsonResponse extends Response
{
    public function __construct(array $body = [])
    {
        parent::__construct(json_encode($body));
    }
}