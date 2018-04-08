<?php

namespace Zefire\Database\Events;

use Zefire\Log\Log;

class Query
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
     * @param  string $statement
     * @param  string $bindings
     * @return void
     */
	public function handle($statement, $bindings)
    {
        if (\App::debugMode() === true) {
        	$this->logger->push('New query: ' . $statement, 'db');
            $data = json_decode($bindings, true);
            if (!empty($data)) {
                $this->logger->push('Query bindings: ' . $bindings, 'db');    
            }            
        }
    }
}