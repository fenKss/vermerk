<?php


namespace App\lib\Http\Routing;


use App\lib\Config\Config;
use ReflectionClass;
use ReflectionException;

class ControllerLoader
{
    private Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Подгружает все php файлы, которые найдет в директории с контроллерами
     */
    public function includeControllers(): void
    {
        $controllersDir = $this->_getControllersDir();
        if (is_string($controllersDir)) {
            $this->_parseControllersTree($controllersDir);
        }
    }

    /**
     * Возвращает директорию, где лежат контроллеры, операясь на namespace
     */
    private function _getControllersDir(): bool|string
    {
        $controllersNamespace = $this->config->controllers->namespace->getData();
        $controllersDir       = str_replace('App', 'src', str_replace('\\', '/', $controllersNamespace));
        return realpath(BASE_DIR . $controllersDir);
    }

    /**
     * Получить Массив контроллеров, в которых можно искать необходимые Route
     *
     * @return array<ReflectionClass>
     * @throws ReflectionException
     */
    public function getControllers(): array
    {
        $this->includeControllers();
        $controllers     = [];
        $declaredClasses = get_declared_classes();
        foreach ($declaredClasses as $declaredClass) {
            $reflectionClass = new ReflectionClass($declaredClass);
            if ($this->_isClassController($reflectionClass) && !$reflectionClass->isAbstract()) {
                $controllers[] = $reflectionClass;
            }
        }
        return $controllers;
    }

    /**
     * Проверяет, является ли класс контроллером
     * Класс должен быть в namespace контроллера, реализовывать интерфейс контроллера <- из конфига
     * Или в названии должен оканчиваться на Controller
     */
    private function _isClassController(ReflectionClass $reflectionClass): bool
    {
        $inNamespace      = $this->_isClassInControllerNamespace($reflectionClass);
        $inInterface      = $this->_isClassImplementControllerInterface($reflectionClass);
        $isControllerName = $this->_isControllerName($reflectionClass->getName());
        /**
         * Если и интерфейс класс реализует, и по namespace подходит - значит это то, что нам нужно
         * Если название заканчивается на Controller - возможно тоже подойдет
         *
         */
        return ($inNamespace && $inInterface) || $isControllerName;


    }

    /*
     * Проверяет, реализует ли контроллер интерфейс из конфига
     */
    private function _isClassImplementControllerInterface(ReflectionClass $reflectionController): bool
    {
        $interface = $this->config->controllers->interface->getData();
        /**
         * Если в конфиге не указан интерфейс - то тогда говорим что мы его реализуем
         */
        if (!isset($interface) || !$interface) {
            return true;
        }
        /**
         * Если в конфиге указан интейрфес - проверяем что класс его реализует
         */
        foreach ($reflectionController->getInterfaces() as $interface) {
            if ($interface->getName() == $interface) {
                return true;

            }
        }
        return false;
    }

    /**
     * Проверяет, находится ли класс в namespace Контроллеров из конфига
     */
    private function _isClassInControllerNamespace(
        ReflectionClass $reflectionController
    ): bool {
        $namespace = $this->config->controllers->namespace->getData();
        /**
         * Namespace должен быть указан. Если его нет - то и контроллер не в нем
         */
        if ($namespace) {
            return str_contains($reflectionController->getNamespaceName(), $namespace);
        }
        return false;
    }

    /**
     * Проверяет,заканчивается ли назвагиние класса на 'Controller'
     */
    private function _isControllerName(
        string $class
    ): bool {
        $controller = 'Controller';
        /** Php < 8.0
         * return substr_compare($class, $controller, strlen($class) - strlen($controller), strlen($controller)) === 0;
         */
        return str_ends_with($class, $controller);

    }

    /**
     * Парсит указанную директорию, и инклудит php файлы, которые найдет
     */
    private function _parseControllersTree(string $dir): void
    {
        $files = scandir($dir);
        foreach ($files as $file) {
            if (in_array($file, ['.', '..'])) {
                continue;
            }
            $fullFilename = $dir . '/' . $file;
            if (is_dir($fullFilename)) {
                $this->_parseControllersTree($fullFilename);
                continue;
            }
            if (pathinfo($fullFilename)['extension']) {
                require_once $fullFilename;
            }
        }
    }
}