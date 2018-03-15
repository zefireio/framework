<?php

namespace Zefire\Contracts;

interface Connectable
{
    /**
     * Saves defined properties of an instance on serialization.
     *
     * @return array
     */
    public function __sleep();
    /**
     * Performs a defined action on unserialization.
     *
     * @return void
     */
    public function __wakeup();
}