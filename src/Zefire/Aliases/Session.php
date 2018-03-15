<?php

namespace Zefire\Aliases;

use Zefire\Alias\Alias;

class Session extends Alias
{
	/**
     * Defines the alias's name
     *
     * @return void
     */
	protected static function serviceName()
    {
		return 'Zefire\\Session\\Session';
	}
}