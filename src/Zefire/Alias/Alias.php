<?php

namespace Zefire\Alias;

use Zefire\Core\Application;

class Alias
{
	/**
     * Provides a static call method for aliases
     *
     * @param  string $method
     * @param  array  $args
     * @return mixed
     */
    public static function __callStatic($method, $args = [])
    {
    	$app = Application::getApp();
        if (static::serviceName() == 'Zefire\Core\Application') {
            $instance = $app;
        } else {
            $instance = $app->make(static::serviceName());
        }
        $return = call_user_func_array([$instance, $method], $args);
        $app->bind(static::serviceName(), $instance);
        return $return;
    }
    /**
     * Defines an alias's name
     *
     * @return void
     */
    protected static function serviceName()
    {

    }
}