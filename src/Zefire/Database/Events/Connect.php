<?php

namespace Zefire\Database\Events;

use Zefire\Log\Log;

class Connect
{
	/**
     * Stores a logger instance.
     *
     * @var \Zefire\Log\Log
     */
	protected $logger;
	/**
     * Creates a new database connection event instance.
     *
     * @param  \Zefire\Log\Log $logger
     * @return void
     */
	public function __construct(Log $logger)
	{
		$this->logger = $logger;
	}
	/**
     * Dispatches an event immediatly.
     *
     * @param  string $status
     * @param  string $dsn
     * @param  string $connection
     * @return void
     */
	public function handle($status, $dsn, $connection)
    {
        if (\App::debugMode() === true) {
        	$this->logger->push($status . ' Connected to ' . $dsn . ' using ' . $connection, 'db');	
        }
    }
}