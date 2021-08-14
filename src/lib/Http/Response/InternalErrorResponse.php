<?php

namespace App\lib\Http\Response;

class InternalErrorResponse extends Response
{
    public function __construct(string $body = '')
    {
        parent::__construct($body, StatusCode::INTERNAL_SERVER_ERROR);
    }
}