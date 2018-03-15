<?php

namespace Zefire\Memcache;

use Zefire\Contracts\Storable;
use Zefire\Core\Serializable;
use Zefire\Contracts\Connectable;

class Memcache implements Storable, Connectable
{
    use Serializable;
    /**
     * Stores a memcache instance.
     *
     * @var \Memcached
     */
    protected $memcache;
    /**
     * Stores the pool of servers.
     *
     * @var array
     */
    protected $serverPool = [];
    /**
     * Creates a new memcache instance.
     *
     * @return void
     */
	public function __construct()
    {
        $this->connect();
    }
    /**
     * Connect to memcache server.
     *
     * @return void
     */
    public function connect()
    {
        $this->memcache = new \Memcached();
        foreach (\App::config('memcache.hosts') as $host => $port) {
            $this->memcache->addServer($host, $port);
        }
    }
    /**
     * Set a value into memcache.
     *
     * @param  string $key
     * @param  string $value
     * @param  int    $ttl
     * @return void
     */
    public function set($key, $value, $ttl = 0)
    {
        $this->memcache->set($key, $value, $ttl);
    }
    /**
     * Checks if a value exists in memcache.
     *
     * @param  string $key
     * @return int
     */
    public function exists($key)
    {
        return ($this->memcache->get($key)) ? 1 : 0;
    }
    /**
     * Gets a value from memcache.
     *
     * @param  string $key
     * @return mixed
     */
    public function get($key)
    {
        return $this->memcache->get($key);
    }
    /**
     * Deletes a value from memcache.
     *
     * @param  string $key
     * @return void
     */
    public function forget($key)
    {
        $this->memcache->delete($key);
    }
    /**
     * Flushes memcache.
     *
     * @return void
     */
    public function flush()
    {
        $this->memcache->fulsh();
    }
    /**
     * Gets all keys from memcache.
     *
     * @return array
     */
    public function all()
    {
        return $this->memcache->getAllKeys();
    }
}