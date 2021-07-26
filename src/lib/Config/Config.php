<?php


namespace App\lib\Config;

/**
 * Класс для работы с конфигами из папки config
 */
class Config implements IConfig
{
    private const CONF_DIR = BASE_DIR . "config";
    private $conf = [];

    public function __construct()
    {
        $this->init();
        dd($this->conf);
    }

    public function get($var)
    {
        // TODO: Implement get() method.
    }

    public function init()
    {
        $dir = realpath(self::CONF_DIR);
        if (!$dir || !is_dir($dir)) {
            throw new \RuntimeException("$dir config dir mus be a directory!");
        }
        $this->parseFilesTree($dir);
    }

    private function parseFilesTree(string $dir)
    {
        $files = scandir($dir);

        foreach ($files as $file) {
            if (in_array($file, ['.', '..'])) {
                continue;
            }

            $fullFilePath = $dir . "/" . $file;
            if (is_dir($fullFilePath)) {
                $this->parseFilesTree($fullFilePath);
                continue;
            }
            $pathInfo    = pathinfo($fullFilePath);
            $filename    = $pathInfo['filename'];
            $parsed_info = match ($pathInfo['extension']) {
                'yaml' => yaml_parse_file($fullFilePath),
            };
            if (isset($parsed_info) && $parsed_info) {
                $this->putData($fullFilePath, $parsed_info);
            }
        }
    }

    private function getArrayPath(string $filepath): array
    {
        $filepath  = explode('.', $filepath)[0];
        $basePath  = realpath(self::CONF_DIR);
        $arrayPath = array_values(array_diff(explode('/', $filepath), explode('/', $basePath)));
        return $arrayPath;

    }

    private function putData(string $fullFilePath, $data)
    {
        $arrayPath  = $this->getArrayPath($fullFilePath);
        var_dump($arrayPath);
        $d = [];
        for($i=count($arrayPath)-1; $i>0; $i--){
//           $d[]
        }
        $this->conf = $data;
    }
}