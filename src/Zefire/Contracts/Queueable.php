<?php

namespace Zefire\Contracts;

interface Queueable
{
    /**
     * Pushes a job on a queue.
     *
     * @param  mixed  $job
     * @param  array  $args
     * @param  string $queue
     * @return void
     */
    public function push(string $job, array $args, string $queue = 'default');
    /**
     * Pushes a job on a queue to be executed at later time.
     *
     * @param  mixed  $job
     * @param  int    $delay
     * @param  array  $args
     * @param  string $queue
     * @return void
     */
    public function later(string $job, int $delay, array $args, string $queue = 'default');
    /**
     * Listens for jobs released on a queue.
     *
     * @param  string $queue
     * @return void
     */
    public function listen(string $queue = 'default');
    /**
     * Clears all jobs on a queue.
     *
     * @param  string $queue
     * @return string
     */
    public function clearQueue(string $queue = 'default');
}
