<?php

namespace Zefire\Session;

use Zefire\Redis\Redis;

class RedisSessionHandler implements \SessionHandlerInterface
{
	/**
     * Stores a redis instance.
     *
     * @var \Zefire\Redis\Redis
     */
    protected $redis;
    /**
     * Creates a new transport instance.
     *
     * @param  \Zefire\Redis\Redis
     * @return void
     */
    public function __construct(Redis $redis)
    {
        $this->redis = $redis;        
    }
    /**
     * Open session save handler callback.
     *
     * @param  string $savePath
     * @param  string $sessionName
     * @return bool
     */
    public function open($savePath, $sessionName)
    {
        return true;
    }
    /**
     * Close session save handler callback.
     *
     * @return bool
     */
    public function close()
    {
        return true;
    }
    /**
     * Read session save handler callback.
     *
     * @param  string $sessionId
     * @return mixed
     */
    public function read($sessionId)
    {
        if (!$this->redis->exists($sessionId)) {
            $this->redis->set($sessionId, '', \App::config('session.life'));
        }
        return $this->redis->get($sessionId);        
    }
    /**
     * Write session save handler callback.
     *
     * @param  string $sessionId
     * @param  mixed  $data
     * @return bool
     */
    public function write($sessionId, $data)
    {
        $this->redis->set($sessionId, $data, \App::config('session.life'));
        return true;
    }
    /**
     * Destroy session save handler callback.
     *
     * @param  string $sessionId
     * @return bool
     */
    public function destroy($sessionId)
    {
        $this->redis->forget($sessionId);
    }
    /**
     * Garbage collection session save handler callback.
     *
     * @param  int $lifetime
     * @return void
     */
    public function gc($lifetime)
    {
        return true;
    }
}