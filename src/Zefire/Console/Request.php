<?php

namespace Zefire\Console;

class Request
{
	/**
     * Command name.
     *
     * @var string
     */
	protected $command;
	/**
     * Command args.
     *
     * @var array
     */
	protected $parameters = [];
	/**
     * Create a new console request instance.
     *
     * @return void
     */
	public function __construct()
	{
		$array = $_SERVER['argv'];
		$this->command = $array[1];
		unset($array[0]);
		unset($array[1]);
		foreach ($array as $parameter) {			
			if (strstr($parameter, '=')) {
				$explode = explode('=', $parameter);
				$this->parameters[str_replace('--', '', $explode[0])] = $explode[1];
			} else {
				$this->parameters[] = str_replace('--', '', $parameter);
			}			
		}
	}
	/**
     * Returns the command name.
     *
     * @return string
     */
	public function command()
	{
		return $this->command;
	}
	/**
     * Returns the command args.
     *
     * @return array
     */
	public function input()
	{
		return $this->parameters;
	}
}