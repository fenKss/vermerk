<?php

use App\Kernel;

include_once "../vendor/autoload.php";
const BASE_DIR = __DIR__ . '/../';

(new Kernel())->run();