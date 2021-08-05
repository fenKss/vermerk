<?php


namespace App\lib\Http\Routing;

use Attribute;

#[Attribute]
class Route
{
    private string  $url;
    private ?string $controller;
    private ?string $method;
    private array   $params = [];
    private array   $requestMethods;

    public function __construct(string $url, array $requestMethods = [])
    {
        $this->url            = $url;
        $this->requestMethods = $requestMethods;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return trim($this->url, '/');
    }

    /**
     * @param string $url
     *
     * @return Route
     */
    public function setUrl(string $url): Route
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return string
     */
    public function getController(): string
    {
        return $this->controller;
    }

    /**
     * @param string $controller
     *
     * @return Route
     */
    public function setController(string $controller): Route
    {
        $this->controller = $controller;
        return $this;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @param string $method
     *
     * @return Route
     */
    public function setMethod(string $method): Route
    {
        $this->method = $method;
        return $this;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @param array $params
     *
     * @return Route
     */
    public function setParams(array $params): Route
    {
        $this->params = $params;
        return $this;
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function setParam(string $key, mixed $value): Route
    {
        $this->params[$key] = $value;
        return $this;
    }

    /**
     * @param string $key
     *
     * @return $this
     */
    public function getParam(string $key): mixed
    {
        return $this->params[$key] ?? null;
    }

    /**
     * @return array
     */
    public function getRequestMethods(): array
    {
        return array_map(function ($method) {
            return mb_strtoupper($method);
        }, $this->requestMethods);
    }

    /**
     * @param array $requestMethods
     *
     * @return Route
     */
    public function setRequestMethods(array $requestMethods): Route
    {
        $this->requestMethods = $requestMethods;
        return $this;
    }

}