<?php

namespace Zefire\Aliases;

use Zefire\Alias\Alias;

class FileSystem extends Alias
{
	/**
     * Defines the alias's name
     *
     * @return void
     */
	protected static function serviceName()
    {
		return 'Zefire\\FileSystem\\FileSystem';
	}
}