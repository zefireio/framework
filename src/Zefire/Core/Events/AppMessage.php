<?php

namespace Zefire\Core\Events;

use Zefire\Log\Log;

class AppMessage
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
     * @param  string $message
     * @return void
     */
	public function handle($message)
    {
        if (\App::debugMode() === true) {
        	$this->logger->push($message, 'app');
        }
    }
}