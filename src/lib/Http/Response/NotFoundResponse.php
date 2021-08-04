<?php

namespace App\lib\Http\Response;

use JetBrains\PhpStorm\Pure;

class NotFoundResponse extends Response
{
    #[Pure] public function __construct(string $body = 'Not Found')
    {
        $statusCode = StatusCode::NOT_FOUND;
        parent::__construct($body, $statusCode);
    }
}