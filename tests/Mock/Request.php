<?php


namespace Test\Mock;


class Request extends \App\lib\Http\Request
{
    private array $get;
    private array $post;
    private array $put;
    private string $method = 'get';
    private string $uri = '';
    private string $queryString = '';

    public function __construct(array $get = [], array $post = [], array $put = [])
    {
        $this->get = $get;
        $this->post = $post;
        $this->put = $put;
    }

    /**
     * Получить все переменные, что пришли в запросе
     */
    public function getAll(): array
    {
        return match (strtolower($this->getMethod())) {
            'get' => $this->get ?? [],
            'post' => $this->post ?? [],
            'put' => $this->put ?? [],
            default => $this->get ?? $this->post ?? [],
        };
    }


    public function getMethod(): string
    {
        return mb_strtoupper($this->method);
    }

    public function setMethod(string $method): void
    {
        $this->method = $method;
    }

    /**
     * Uri запроса вместе с get параметрами
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * Uri запроса вместе с get параметрами
     */
    public function setUri(string $uri): self
    {
        $this->uri = $uri;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getQuery(): string
    {
        return $this->queryString;
    }

    /**
     * @return array
     */
    public function getGet(): array
    {
        return $this->get;
    }

    /**
     * @param array $get
     * @return Request
     */
    public function setGet(array $get): Request
    {
        $this->get = $get;
        return $this;
    }

    /**
     * @return array
     */
    public function getPost(): array
    {
        return $this->post;
    }

    /**
     * @param array $post
     * @return Request
     */
    public function setPost(array $post): Request
    {
        $this->post = $post;
        return $this;
    }

    /**
     * @return array
     */
    public function getPut(): array
    {
        return $this->put;
    }

    /**
     * @param array $put
     * @return Request
     */
    public function setPut(array $put): Request
    {
        $this->put = $put;
        return $this;
    }


    /**
     * @param string $queryString
     * @return Request
     */
    public function setQuery(string $queryString): Request
    {
        $this->queryString = $queryString;
        return $this;
    }


}