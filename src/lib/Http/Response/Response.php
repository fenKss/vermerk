<?php


namespace App\lib\Http\Response;

class Response
{

    private string $body;
    private int    $statusCode;
    private array  $headers;
    private string $contentType = 'text/html; charset=UTF-8';

    public function __construct(
        string $body = '',
        int    $statusCode = StatusCode::OK,
        array  $headers = [],
    ) {
        $this->body       = $body;
        $this->statusCode = $statusCode;
        $this->headers    = $headers;
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

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        $headers = [];

        foreach ($this->headers as $key => $header) {
            $headers[ucfirst($key)] = $header;
        }
        if (!isset($headers['Content-Type'])) {
            $headers['Content-Type'] = 'text/html; charset=UTF-8';
        }
        return $headers;
    }

    /**
     * @param array $headers
     *
     * @return Response
     */
    public function setHeaders(array $headers): Response
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * @param string $header
     * @param mixed  $val
     */
    public function setHeader(string $header, mixed $val)
    {
        $this->headers[ucfirst($header)] = $val;
    }

    /**
     * @param string $contentType
     */
    public function setContentType(string $contentType)
    {
        $this->setHeader('Content-Type', $contentType);
    }

}