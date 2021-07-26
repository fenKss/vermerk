<?php


namespace App\lib\Config;

/**
 * Класс для работы с .env конфигом
 */
class DotenvConfig implements IConfig
{

    public function __construct()
    {
        $this->init();
    }

    /**
     * @inheritDoc
     */
    public function get($var)
    {
        return $_ENV[$var] ?? null;
    }

    private function init(): void
    {
        $dotenv = \Dotenv\Dotenv::createImmutable(BASE_DIR);
        $dotenv->load();
    }
}