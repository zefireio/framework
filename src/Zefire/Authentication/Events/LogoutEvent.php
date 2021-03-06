<?php

namespace Zefire\Authentication\Events;

use Zefire\Log\Log;

class LogoutEvent
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
     * @param  string $email
     * @return void
     */
	public function handle($email)
    {
        if (\App::debugMode() === true) {
        	$this->logger->push($email . ' logged out', 'app');
        }
    }
}