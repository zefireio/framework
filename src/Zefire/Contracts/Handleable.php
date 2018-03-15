<?php

namespace Zefire\Contracts;

interface Handleable
{
    /**
     * General method acting as a hook.
     *
     * @return void
     */
    public function handle();
}