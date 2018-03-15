<?php

namespace Zefire\Aliases;

use Zefire\Alias\Alias;

class Route extends Alias
{
	/**
     * Defines the alias's name
     *
     * @return void
     */
	protected static function serviceName()
    {
		return 'Zefire\\Routing\\Route';
	}
}