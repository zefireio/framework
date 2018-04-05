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
     * @param  string $options
     * @return void
     */
	public function handle($status, $dsn, $options = false)
    {
        if (\App::debugMode() === true) {
        	if ($status) {
                $this->logger->push('Connected to ' . $dsn, 'db');
                if ($options) {
                    $this->logger->push('Options: ' . $options, 'db');
                }
            } else {
                $this->logger->push('Failed to connect to ' . $dsn, 'db');
                if ($options) {
                    $this->logger->push('Options: ' . $options, 'db');
                }
            }
            
        }
    }
}