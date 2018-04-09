<?php

namespace Zefire\Redis;

use \Predis\Client;
use Zefire\Contracts\Storable;
use Zefire\Core\Serializable;
use Zefire\Contracts\Connectable;

class Redis implements Storable, Connectable
{
    use Serializable;
    /**
     * Stores a predis client instance.
     *
     * @var \Predis\Client
     */
    protected $predis;
    /**
     * Stores the store name.
     *
     * @var string
     */
    protected $store;
    /**
     * Creates a new redis instance.
     *
     * @param  string $store
     * @return void
     */
	public function __construct($store = false)
    {
        $this->store = ($store) ? $store : 'default';
        $this->connect();
    }
    /**
     * Connect to redis server.
     *
     * @return void
     */
    public function connect()
    {
        $config = \App::config('redis');
        $explode = explode(':', $config['server']);
        $this->predis = new Client([
            'scheme' => $config['scheme'],
            'host'   => $explode[0],
            'port'   => $explode[1],
        ]);
    }
    /**
     * Set a value into redis.
     *
     * @param  string $key
     * @param  string $value
     * @param  int    $ttl
     * @return void
     */
    public function set($key, $value, $ttl = 0)
    {
        $this->predis->hset($this->store, $key, serialize($value));
        $this->predis->expire($key, $ttl);
    }
    /**
     * Checks if a value exists in redis.
     *
     * @param  string $key
     * @return bool
     */
    public function exists($key)
    {
        return (unserialize($this->predis->hget($this->store, $key))) ? true : false;
    }
    /**
     * Gets a value from redis.
     *
     * @param  string $key
     * @return mixed
     */
    public function get($key)
    {
        return unserialize($this->predis->hget($this->store, $key));
    }
    /**
     * Deletes a value from redis.
     *
     * @param  string $key
     * @return void
     */
    public function forget($key)
    {
        return $this->predis->hdel($key);
    }
    /**
     * Flushes redis.
     *
     * @return void
     */
    public function flush()
    {
        return $this->predis->hdel($this->all());
    }
    /**
     * Gets all keys from redis.
     *
     * @return array
     */
    public function all()
    {
        return $this->predis->hkeys($this->store);
    }
    /**
     * Gets all data from redis.
     *
     * @return array
     */
    public function getAllData()
    {        
        $array = [];
        foreach ($this->predis->hgetall($this->store) as $key => $value) {
            $array[$key] = unserialize($value);
        }
        return $array;
    }
    /**
     * Pushes a job on the end of a queue.
     *
     * @param  string $queue
     * @param  string $job
     * @return void
     */
    public function push($queue, $job)
    {
        $this->predis->rpush($queue, $job);
    }
    /**
     * Pops a job from a queue.
     *
     * @param  string  $queue
     * @param  string  $processing_queue
     * @param  integer $wait
     * @return string
     */
    public function pop($queue, $processing_queue, $wait = 10)
    {
        return $this->predis->brpoplpush($queue, $processing_queue, $wait);
    }
    /**
     * Deletes successful jobs from the queue.
     *
     * @param  string  $queue
     * @param  string  $job
     * @return string
     */
    public function success($queue, $job)
    {
        return $this->predis->lrem($queue, 1, $job);
    }
    /**
     * Deletes failed jobs from the queue
     * and pushes them onto the failed jobs queue.
     *
     * @param  string  $queue
     * @param  string  $job
     * @return string
     */
    public function failed($queue, $job)
    {
        $this->predis->lpush($queue, $job);
        $this->predis->lrem($queue);
    }
    /**
     * Flushes a queue.
     *
     * @param  string  $queue
     * @return void
     */
    public function flushQueue($queue)
    {
        $this->predis->del($queue);
    }
    /**
     * Json decodes a job and returns an object to process it.
     *
     * @param  string  $job
     * @return \stdClass
     */
    public function parseJob($job)
    {
        return json_decode($job);
    }
    /**
     * Publish a message to a channel.
     *
     * @param  string  $channel
     * @param  string  $message
     * @return void
     */
    public function publish($channel, $message)
    {
        $this->client->publish($channel, serialize($message));
    }
    /**
     * Subscribe a handler to a channel.
     *
     * @param  string   $channel
     * @param  callable $handler
     * @return void
     */
    public function subscribe($channel, callable $handler)
    {
        $loop = $this->predis->pubSubLoop();
        $loop->subscribe($channel);
        foreach ($loop as $message) {
            if ($message->kind === 'message') {
                call_user_func($handler, unserialize($message->payload));
            }
        }
        unset($loop);
    }
}