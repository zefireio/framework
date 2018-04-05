<?php

namespace Zefire\Event;

use Zefire\Queue\Queue;

class Dispatcher
{
    /**
     * Stores a queueing instance.
     *
     * @var \Zefire\Queue\Queue
     */
    protected $queue;
    /**
     * Stores events.
     *
     * @var array
     */
    protected $events;
    /**
     * Creates a new event dispatcher instance.
     *
     * @param  \Zefire\Queue\Queue $queue
     * @return void
     */
    public function __construct(Queue $queue)
    {
        $this->queue = $queue;
        $this->events = \App::config('events');
    }
    /**
     * Dispatches an event immediatly.
     *
     * @param  string $name
     * @param  array  $args
     * @return mixed
     */
    public function now($name, $args = [])
    {
        if (isset($this->events[$name])) {
            $event = \App::make($this->events[$name]);
            return call_user_func_array([$event, 'handle'], $args);
        } else {
            throw new \Exception('Could not find event in container');
        }
    }
    /**
     * Dispatches an event on a queue.
     *
     * @param  string $name
     * @param  array  $args
     * @param  string $queue
     * @return void
     */
    public function queue($name, $args = [], $queue = 'events')
    {
        if (isset($this->events[$name])) {
            $this->queue->push($this->events[$name], $args, $queue);
        } else {
            throw new \Exception('Could not find event in container');
        }
    }
    /**
     * Dispatches an event on a queue at later time.
     *
     * @param  string $name
     * @param  int    $delay
     * @param  array  $args
     * @param  string $queue
     * @return void
     */
    public function later($name, $delay, $args = [] , $queue = 'events')
    {
        if (isset($this->events[$name])) {
            $this->queue->later($this->events[$name], $delay, $args, $queue);
        } else {
            throw new \Exception('Could not find event in container');
        }
    }
    /**
     * Removes an event from dispatcher.
     *
     * @param  string $name
     * @return void
     */
    public function forget($name)
    {
        if (isset($this->events[$name])) {
            unset($this->events[$name]);
        }
    }
    /**
     * Flushes the dispatcher of all events.
     *
     * @return void
     */
    public function flush()
    {
        $this->events = [];        
    }
    /**
     * Lists all events from dispatcher.
     *
     * @return array
     */
    public function all()
    {
        return $this->events;
    }
}