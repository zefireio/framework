<?php

namespace Zefire\Core;

class Container
{
	/**
     * Stores all container bindings.
     *
     * @var array
     */
    protected $bindings = [];
    /**
     * Binds a service to container.
     *
     * @param  string $key
     * @param  object $instance
     * @return void
     */
    public function bind($key, $instance)
    {
        $this->bindings[$key] = serialize($instance);
    }    
    /**
     * Registers a new alias.
     *
     * @param  string $name
     * @param  string $class
     * @return void
     */
	public function registerAlias($name, $class)
    {
        class_alias($class, $name);        
    }
    /**
     * Checks if a service is bounded to container.
     *
     * @param  string $key
     * @return bool
     */
    public function exists($key)
    {
        return (isset($this->bindings[$key])) ? 1 : 0;
    }
    /**
     * Retrieves a service from container.
     *
     * @param  string $key
     * @return object
     */
    public function get($key)
    {
        return ($this->exists($key)) ? unserialize($this->bindings[$key]) : null;
    }
    /**
     * Deletes a service from container.
     *
     * @param  string $key
     * @return void
     */
    public function forget($key)
    {
        if ((isset($this->bindings[$key]))) {
            unset($this->bindings[$key]);    
        }        
    }
    /**
     * Flushes all services from container.
     *
     * @return void
     */
    public function flush()
    {
        $this->bindings = [];
    }
    /**
     * Creates a new instance of a service (or pulls from container if already exists) with
     * dependencies and binds it to the container.
     *
     * @param  string $class
     * @param  array  $params
     * @return object
     */
    public function make($class, $params)
    {
        if ($this->exists($class)) {
            return $this->get($class);
        } else {
            $reflector = new \ReflectionClass($class);
            if ($reflector->isInstantiable()) {
                $constructor = $reflector->getConstructor();
                if (!$constructor) {
                    $instance = $reflector->newInstanceWithoutConstructor();
                    $this->bind($class, $instance);
                    return $instance;
                } else {
                    $parameters = $constructor->getParameters();
                    $dependencies = [];
                    foreach($parameters as $parameter) {
                        $dependency = $parameter->getClass();
                        if ($dependency) {
                            if ($this->exists($dependency->name)) {
                                $dependencies[] = $this->get($dependency->name);
                            } else {
                                $dependency_instance = $this->make($dependency->name, [], false);
                                $this->bind($dependency->name, $dependency_instance);
                                $dependencies[] = $dependency_instance;
                            }                            
                        }
                    }
                    $instance = $reflector->newInstanceArgs(array_merge($dependencies, $params));
                    $this->bind($class, $instance);
                    return $instance;
                }
            } else {
                throw new \Exception($class . ' is not instantiable');
            }
        }        
    }
    /**
     * Resolves dependencies (or pulls them from container) and binds them to the container.
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
                if ($this->exists($dependency->name)) {
                    $dependencies[] = $this->get($dependency->name);
                } else {
                    $dependency_instance = $this->make($dependency->name, [], false);
                    $this->bind($dependency->name, $dependency_instance);
                    $dependencies[] = $dependency_instance;
                }                            
            }
        }
        return $dependencies;
    }
}