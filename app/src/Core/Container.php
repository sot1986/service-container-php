<?php

declare(strict_types=1);

namespace App\Core;

use App\Exceptions\ContainerException;
use App\Exceptions\NotFoundException;
use ReflectionMethod;

class Container implements \Psr\Container\ContainerInterface
{
    protected static array $singletons = [];

    protected array $entries = []; // ['App\Services\InvoiceService' => ...]

    public function get(string $key)
    {
        if (!$this->has($key)) {
            return $this->resolve($key);
        }

        if (array_key_exists($key, self::$singletons)) {
            return $this->getSingleton($key);
        }

        return $this->entries[$key]($this);
    }

    public function has(string $key)
    {
        if (array_key_exists($key, self::$singletons)) {
            return true;
        }

        if (array_key_exists($key, $this->entries)) {
            return true;
        }

        return false;
    }

    private function getSingleton(string $key)
    {
        // Check if the singleton is a callable
        if (is_callable(self::$singletons[$key])) {
            // If it is, call it and store the result, to avoid calling it again in future
            self::$singletons[$key] = self::$singletons[$key]($this);
        }

        return self::$singletons[$key];
    }

    public function set(string $key, callable $value)
    {
        $this->entries[$key] = $value;
    }

    public function singleton(string $key, callable $value)
    {
        self::$singletons[$key] = $value;
    }

    public function resolve(string $key)
    {
        // Ispezionare $key
        $class = new \ReflectionClass($key);

        // Se non è istanziabile, lanciare un'eccezione
        if (!$class->isInstantiable()) {
            throw new ContainerException("Class $key is not instantiable");
        }

        // Se non ha costruttore, istanziare la classe
        $constructor = $class->getConstructor();

        if (is_null($constructor)) {
            return $class->newInstance();
        }

        // Se il construttore non ha parametri, istanziare la classe
        $parameters = $constructor->getParameters();

        if (count($parameters) === 0) {
            return $class->newInstance();
        }

        // Se il construttore ha parametri, risolverli
        $dependencies = array_map(
            function (\ReflectionParameter $parameter) {
                $name = $parameter->getName();
                $type = $parameter->getType();

                if (is_null($type)) {
                    throw new ContainerException("Unable to resolve dependency $name");
                }

                if ($type instanceof \ReflectionUnionType) {
                    throw new ContainerException("Unable to resolve union type for $name");
                }

                if ($type instanceof \ReflectionNamedType && !$type->isBuiltin()) {
                    $type = $type->getName();

                    return $this->get($type);
                }

                throw new ContainerException("Unable to resolve dependency $name");
            },
            $parameters
        );

        return $class->newInstanceArgs($dependencies); // new InvoiceService(...$dependencies)
    }

    public function resolveFromMethod(string $className, string $methodName)
    {
        $methd = new ReflectionMethod($className, $methodName);

        $parameters = $methd->getParameters();

        if (count($parameters) === 0) {
            return $methd->invoke($this->get($className));
        }

        // ...
    }
}
