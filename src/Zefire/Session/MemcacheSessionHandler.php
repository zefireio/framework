<?php

namespace Zefire\Session;

use Zefire\Memcache\Memcache;

class MemcacheSessionHandler implements \SessionHandlerInterface
{
	/**
     * Stores a memcache instance.
     *
     * @var \Zefire\Memcache\Memcache
     */
    protected $memcache;
    /**
     * Creates a new transport instance.
     *
     * @param  \Zefire\Memcache\Memcache
     * @return void
     */
    public function __construct(Memcache $memcache)
    {
        $this->memcache = $memcache;
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
        $session = $this->memcache->get($sessionId);
        if ($session) {
            return $session;
        } else {
            $this->memcache->set($sessionId, '', \App::config('session.life'));
            return $this->memcache->get($sessionId);
        }        
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
        $this->memcache->set($sessionId, $data, \App::config('session.life'));
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
        $this->memcache->forget($sessionId);
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