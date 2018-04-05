<?php

namespace Zefire\Authentication\Events;

use Zefire\Log\Log;

class AuthEvent
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
     * @param  bool   $status
     * @param  string $email
     * @return void
     */
	public function handle($status, $email)
    {
        if (\App::debugMode() === true) {
        	if ($status) {
                $this->logger->push($email . ' logged in', 'app');    
            } else {
                $this->logger->push($email . ' failed to log in', 'app');
            }            
        }
    }
}