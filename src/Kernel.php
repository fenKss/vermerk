<?php


namespace App;


use App\lib\Config;
use App\lib\Di\Container;

class Kernel
{
    private Container $container;

    public function __construct()
    {
        $this->container = new Container([
            Config\DotenvConfig::class,
            Config\Config::class
        ]);
    }

    public function run()
    {

    }

}