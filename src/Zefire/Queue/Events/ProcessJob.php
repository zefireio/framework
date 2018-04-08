<?php

namespace Zefire\Queue\Events;

use Zefire\Log\Log;

class ProcessJob
{
	/**
     * Stores a logger instance.
     *
     * @var \Zefire\Log\Log
     */
	protected $logger;
	/**
     * Creates a process job event instance.
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
     * @param  string $job
     * @return void
     */
	public function handle($job)
    {
        $this->logger->push('Processing "' . $job . '" job', 'queue');
    }
}