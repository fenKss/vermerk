<?php


namespace App\lib\Config;


interface IConfig
{
    /**
     * Получение элемента из конфига
     */
    public function get($var);

}