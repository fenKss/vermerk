<?php


namespace App;


use App\lib\Config;

class Kernel
{
    public function run()
    {
        $this->initConfigs();
    }

    /**
     * Инициализирует конфиги
     */
    private function initConfigs(): void
    {
        (new Config\DotenvConfig());
        (new Config\Config());
    }

}