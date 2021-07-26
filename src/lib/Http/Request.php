<?php


namespace App\lib\Http;


class Request
{
    /**
     * Получает переменную из запроса
     */
    public function get(string $var): ?string
    {
        return $this->getAll()[$var] ?? null;
    }

    /**
     * Получить все переменные, что пришли в запросе
     */
    public function getAll(): array
    {
        switch (strtolower($this->getMethod())) {
            case 'get':
                return $_GET ?? [];
            case 'post';
                return $_POST ?? [];
            case 'put':
                parse_str(file_get_contents("php://input"), $put_vars);
                return $put_vars ?? [];
            default:
                return $_GET ?? $_POST ?? [];
        }
    }

    /**
     * Возвращает метод запроса
     */
    public function getMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Uri запроса вместе с get параметрами
     */
    public function getUri(): string
    {
        return $_SERVER['REQUEST_URI'];
    }

    /**
     * get парметры в виде строки
     */
    public function getQuery(): string
    {
        return $_SERVER['QUERY_STRING'];
    }

    /**
     * Возвращает чистый урл
     */
    public function getUrl(): string
    {
        return explode('?', $this->getUri())[0];
    }
}