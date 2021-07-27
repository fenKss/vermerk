<?php


namespace App\lib\Http\Response;


class Response
{

    private string $body;
    private int    $statusCode;

    public function __construct(
        string $body = '',
        int $statusCode = StatusCode::OK
    ) {
        $this->body       = $body;
        $this->statusCode = $statusCode;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;
        return $this;
    }

    public function setStatusCode(int $statusCode): self
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

}