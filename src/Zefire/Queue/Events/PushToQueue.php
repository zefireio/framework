<?php

namespace Zefire\Queue\Events;

use Zefire\Log\Log;

class PushToQueue
{
	/**
     * Stores a logger instance.
     *
     * @var \Zefire\Log\Log
     */
	protected $logger;
	/**
     * Creates a push on queue event instance.
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
     * @param  string $queue
     * @param  string $job
     * @return void
     */
	public function handle($queue, $job)
    {
        $this->logger->push('Pushing "' . $job . '" job onto "' . $queue . '" queue', 'queue');
    }
}