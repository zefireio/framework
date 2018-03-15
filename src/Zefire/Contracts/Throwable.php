<?php

namespace Zefire\Contracts;

interface Throwable
{
    /**
     * Handles exceptions.
     *
     * @param  \Exception $exception
     * @return void
     */
    public static function handleException($exception);
    /**
     * Handles errors.
     *
     * @param  int    $level
     * @param  string $message
     * @param  string $file
     * @param  int    $line
     * @param  array  $context
     * @return void
     */
    public static function handleError($level, $message, $file = '', $line = 0, $context = []);
    /**
     * Handles shutdowns.
     *
     * @return void
     */
    public static function handleShutdown();
}
