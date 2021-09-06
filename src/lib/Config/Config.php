<?php


namespace App\lib\Config;


/**
 * Класс для работы с конфигами из папки config
 *
 * @property ConfigShard controllers
 */
class Config implements IConfig
{
    private array  $conf = [];
    private string $path;
    private ?string $env;

    public function __construct()
    {
        $this->env = $_ENV['ENV'] ?? $_ENV['env'] ?? null;
        $this->path = realpath(BASE_DIR . 'config');
        $this->parseFilesTree($this->path);
    }

    /**
     * Получить нужный объект из корня
     */
    public function get(string $var): ConfigShard
    {
        $data = $this->conf[$this->env][$var] ?? $this->conf[$var] ?? null;
        return new ConfigShard($data);
    }

    /**
     * Магический __get для использования массива как объекта
     */
    public function __get(string $name)
    {
        return $this->get($name);
    }

    /**
     * Парсит дерево папок и добавляет данные в $this->conf
     */
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
            $parsed_info = match ($pathInfo['extension']) {
                'yaml' => yaml_parse_file($fullFilePath),
                'php' => include $fullFilePath
            };
            if (isset($parsed_info) && $parsed_info) {
                $this->putData($fullFilePath, $parsed_info);
            }
        }
    }

    /**
     * Получаем массив из папок, которые должны отразиться в пути массива
     */
    private function getArrayPath(string $filepath): array
    {
        /**
         * Оставляем только путь, обрезаем расширение
         */
        $filepath = explode('.', $filepath)[0];
        /**
         * Убираем лишние папки, которые могут быть, e.g название проекта, config
         */
        return array_values(array_diff(explode('/', $filepath), explode('/', $this->path)));

    }

    /**
     * Кладет данные по переданному пути в $this->conf
     */
    private function putData(string $fullFilePath, $data)
    {
        $arrayPath = $this->getArrayPath($fullFilePath);
        /**
         * Если в пути есть папки - формируем массив с хвоста
         */
        $data = $this->normalizePaths($arrayPath, $data);

        $rootPath              = $arrayPath[0];
        $this->conf[$rootPath] = array_merge_recursive($data, $this->conf[$rootPath] ?? []);
    }

    /**
     * Нормализует пути в массиве e.g test/another/asd:{"data":"123"} -> [test][another][asd][data]=>123
     */
    private function normalizePaths(array $arrayPath, array $data): array
    {
        $arrayPathCount = count($arrayPath);
        if ($arrayPathCount <= 1) {
            return $data;
        }
        /**
         * Последний элемент должен содержать распаршенную инфу
         */
        $tempData[end($arrayPath)] = $data;
        for ($i = $arrayPathCount - 2; $i > 0; $i--) {
            /** Идем в обратном порядке и подменяем массивы что бы нормализовать пути */
            $temp[$arrayPath[$i]] = $tempData;
            $tempData             = $temp;
        }
        return $tempData;
    }
}