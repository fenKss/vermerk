<?php

include_once "../src/bootstrap.php";

use App\Kernel;
use App\lib\Config\DotenvConfig;

(new DotenvConfig())->init();
(new Kernel())->run();