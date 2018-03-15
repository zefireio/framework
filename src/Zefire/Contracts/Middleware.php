<?php

namespace Zefire\Contracts;

interface Middleware
{
    /**
     * Provides a hook for middilewares.
     *
     * @return void
     */
    public function handle();
}