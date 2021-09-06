<?php


namespace App\lib\Config;

/**
 * Класс для работы с .env конфигом
 */
class DotenvConfig implements IConfig
{
    public static bool      $isInitialized = false;
    private ?\Dotenv\Dotenv $dotenv        = null;

    /**
     * @inheritDoc
     */
    public function get($var): ConfigShard
    {
        return new ConfigShard($_ENV[$var] ?? null);
    }

    public function init(): void
    {
        if (!self::$isInitialized) {
            $this->dotenv = \Dotenv\Dotenv::createImmutable(BASE_DIR);
            $this->dotenv->load();
            self::$isInitialized = true;
        }

    }
}