<?php

use App\lib\Config\DotenvConfig;

const BASE_DIR = __DIR__ . '/../';
require_once BASE_DIR. 'vendor/autoload.php';
(new DotenvConfig())->init(BASE_DIR."tests");