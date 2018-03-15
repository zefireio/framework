<?php

namespace Zefire\Factory;

class Factory
{
	/**
     * Creates a new instance of a service with its
     * dependencies and returns it.
     *
     * @param  string $class
     * @param  array  $params
     * @return object
     */
    public function make($class, $params = [])
    {
        $reflector = new \ReflectionClass($class);
        if ($reflector->isInstantiable()) {
            $constructor = $reflector->getConstructor();
            if (!$constructor) {
                return $reflector->newInstanceWithoutConstructor();
            } else {
                $parameters = $constructor->getParameters();
                $dependencies = [];
                foreach($parameters as $parameter) {
                    $dependency = $parameter->getClass();
                    if ($dependency) {
                        $dependencies[] = $this->make($dependency->name, [], false);
                    }
                }
                return $reflector->newInstanceArgs(array_merge($dependencies, $params));
            }
        } else {
            throw new \Exception($class . ' is not instantiable');
        }
    }
    /**
     * Resolves dependencies required by a service method.
     *
     * @param  string $class
     * @param  string $method
     * @return array
     */
    public function resolveMethodDependencies($class, $method)
    {
        $reflector = new \ReflectionClass($class);
        $parameters = $reflector->getMethod($method)->getParameters();
        $dependencies = [];
        foreach ($parameters as $parameter) {
            $dependency = $parameter->getClass();
            if ($dependency) {
                $dependencies[] = $this->make($dependency->name, [], false);
            }
        }
        return $dependencies;
    }
}