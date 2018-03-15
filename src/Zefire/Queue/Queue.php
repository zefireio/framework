<?php

namespace Zefire\Queue;

use Zefire\Contracts\Queueable;

class Queue implements Queueable
{
	/**
     * Pushes a job on a queue.
     *
     * @var mixed
     */
	protected $driver;
	/**
     * Creates a new queue instance.
     *
     * @return void
     */
    public function __construct()
    {
        try {
            $this->driver = \App::make(\App::config('queueing.driver'));
        } catch (\Exception $e) {
            $this->driver = \App::make(\Zefire\Queue\PheanstalkHandler::class);
        }
    }
    /**
     * Pushes a job on a queue.
     *
     * @param  string $job
     * @param  array  $args
     * @param  string $queue
     * @return void
     */
	public function push(string $job, array $args, string $queue = 'default')
	{
		$this->driver->push($this->prepareJob($job, 0, $args), $queue);
	}
	/**
     * Pushes a job on a queue to be executed at later time.
     *
     * @param  mixed  $job
     * @param  int    $delay
     * @param  array  $args
     * @param  string $queue
     * @return void
     */
	public function later(string $job, int $delay, array $args, string $queue = 'default')
	{
		$this->driver->push($this->prepareJob($job, $delay, $args), $queue);
	}
	/**
     * Listens for jobs released on a queue.
     *
     * @param  string $queue
     * @return void
     */
	public function listen(string $queue = 'default')
	{
		$this->driver->listen($queue);        
	}
    /**
     * Clears all jobs on a queue.
     *
     * @param  string $queue
     * @return string
     */
    public function clearQueue(string $queue = 'default')
    {
        return $this->driver->clearQueue($queue);
    }
	/**
     * Prepares and formats a job.
     *
     * @param  string $job
     * @param  int    $delay
     * @param  array  $args
     * @return string
     */
	protected function prepareJob(string $job, int $delay = 0, array $args)
	{
		$array = [
			'name'  => $job,
            'args'  => json_encode($args),
			'tries' => \App::config('queueing.tries'),
			'delay' => time() + $delay
		];
        return json_encode($array);
	}
}