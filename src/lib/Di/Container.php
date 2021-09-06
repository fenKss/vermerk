<?php


namespace App\lib\Di;

use App\lib\Config\Config;
use App\lib\Config\DotenvConfig;
use ReflectionClass;
use ReflectionException;
use ReflectionParameter;
use RuntimeException;

class Container
{
    /**
     * @var array<object>
     */
    public array $singletons;
    /**
     * @var array<string>
     */
    private array $interfaceMapping;

    public function __construct()
    {
        $config = new Config();
        $singletons = $config->container->singletons ?? [];
        $this->interfaceMapping = (array)$config->container->interfaceMapping;
        foreach ($singletons as $singleton) {
            $this->singletons[$singleton] = null;
        }
        $this->singletons[self::class] = $this;
    }

    /**
     * @throws ReflectionException
     */
    public function get(string $className)
    {
        if ($this->isMappedInterface($className)) {
            $className = $this->interfaceMapping[$className];
        }
        if ($this->isSingleton($className) && !is_null($this->singletons[$className])) {
            return $this->singletons[$className];
        }
        return $this->_get($className);
    }

    /**
     * @throws ReflectionException
     */
    private function _get(string $className)
    {
        $reflectionClass = new ReflectionClass($className);
        $instance        = new $className(...$this->_getParameters($reflectionClass));
        if ($this->isSingleton($className)) {
            $this->singletons[$className] = $instance;
        }
        return $instance;
    }

    /**
     * Получает нужное значение для параметра
     *
     * @throws ReflectionException
     */
    private function _getValueFromParameter(ReflectionParameter $parameter)
    {
        $parameterType = (string)$parameter->getType();
        /**
         * Если это интерфейс - сначала получаем его реализацию
         */
        if (interface_exists($parameterType)) {
            $parameterType = $this->_getInterfaceImplementation($parameterType);
        }
        /**
         * Если это класс - получаем его из контейнера
         */
        if (class_exists($parameterType)) {
            return $this->get($parameterType);
        }
        /**
         * Если это обычное значение - берем из $_ENV
         */
        $value = $this->get(DotenvConfig::class)->get($parameter->getName());
        /**
         * Не смогли найти что подставить
         */
        if (is_null($value)) {
            throw new RuntimeException("Can't assign ($parameterType {$parameter->getName()}) value");
        }
        return $value;
    }

    /**
     * Возвращает класс реализующий переданный интерфейс
     */
    private function _getInterfaceImplementation(string $interfaceName): string
    {
        $interfaceImplementation = $this->interfaceMapping[$interfaceName] ?? null;
        if (is_null($interfaceImplementation)) {
            throw new RuntimeException("Can't get $interfaceName implementation");
        }
        return $interfaceImplementation;
    }

    /**
     * Возвращает массив параметров для класса
     *
     * @throws ReflectionException
     */
    private function _getParameters(ReflectionClass $reflectionClass): array
    {
        $constructor = $reflectionClass->getConstructor();
        /**
         * Если нет конструктора, то и параметров тоже нет
         */
        if (!$constructor) {
            return [];
        }
        $parameters = [];
        foreach ($constructor->getParameters() as $parameter) {
            try {
                $parameters[$parameter->getName()] = $parameter->getDefaultValue();
            } catch (ReflectionException) {
                $parameters[$parameter->getName()] = $this->_getValueFromParameter($parameter);
            }

        }
        return $parameters;
    }

    private function isMappedInterface(string $interface): bool
    {
        return ($this->interfaceMapping[$interface] ?? null) && interface_exists($interface);
    }

    private function isSingleton(string $singleton): bool
    {
        return in_array($singleton, array_keys($this->singletons));
    }
}