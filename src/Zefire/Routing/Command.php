<?php

namespace Zefire\Routing;

class Command
{
	/**
     * Stores a list of registered commands.
     *
     * @var array
     */
	protected $commands = [];
	/**
     * Registers a new command.
     *
     * @param  string $name
     * @param  array  $action
     * @return \Zefire\Routing\Command
     */
	public function name($name, $action)
	{
		$this->commands[$name] = [
			'name' => $name,
			'action' => $action
		];
		return $this;
	}
	/**
     * Gets all registered commands.
     *
     * @return array
     */
	public function getCommands()
	{
		return $this->commands;
	}
}