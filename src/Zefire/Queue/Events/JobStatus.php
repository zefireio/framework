<?php

namespace Zefire\Queue\Events;

use Zefire\Log\Log;

class JobStatus
{
	/**
     * Stores a logger instance.
     *
     * @var \Zefire\Log\Log
     */
	protected $logger;
	/**
     * Creates a job status event instance.
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
     * @param  mixed  $job
     * @param  string $status
     * @return void
     */
	public function handle($queue, $job, $status)
    {
        if (is_object($job)) {
            $job = json_decode($job->getData());
        }
        if (is_string($job)) {
            $job = json_decode($job);
        }        
        switch ($status) {
            case 1:
                $this->logger->push('Successfuly processed "' . $job->name . '" from "' . $queue . '" queue ', 'queue');
                break;
            case 2:
                $this->logger->push('Failed to process "' . $job->name . '" Job from "' . $queue . '" queue ', 'queue');
                break;
            case 3:
                $this->logger->push('Buried "' . $job->name . '" Job from queue "' . $queue . '" ', 'queue');
                break;
        }   
    }
}