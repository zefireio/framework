<?php

namespace Zefire\Cache;

use Zefire\Contracts\Storable;

class Cache implements Storable
{
    /**
     * Holds the caching driver instance.
     *
     * @var mixed
     */
    protected $driver;
    /**
     * Create a new Cache instance.
     *
     * @return void
     */
    public function __construct()
    {
        try {
            $this->driver = \App::make(\App::config('cache.driver'));
        } catch (\Exception $e) {
            $this->driver = \App::make('Zefire\Memcache\Memcache');
        }
    }
    /**
     * Sets a new entry into cache.
     *
     * @param. string $key
     * @param. string $value
     * @param. int    $ttl
     * @return void
     */
    public function set($key, $value, $ttl = 60)
    {
        $this->driver->set($key, $value, $ttl);
    }
    /**
     * Checks if a key exists in cache.
     *
     * @param. string $key
     * @return int
     */
    public function exists($key)
    {
        return ($this->driver->get($key)) ? 1 : 0;
    }
    /**
     * Gets an entry from cache.
     *
     * @param. string $key
     * @return mixed
     */
    public function get($key)
    {
        return $this->driver->get($key);
    }
    /**
     * Deletes an entry from cache.
     *
     * @param. string $key
     * @return void
     */
    public function forget($key)
    {
        $this->driver->forget($key);
    }
    /**
     * Flushes cache.
     *
     * @return void
     */
    public function flush()
    {
        $this->driver->fulsh();
    }
    /**
     * Gets all entries from cache.
     *
     * @return mixed
     */
    public function all()
    {
        return $this->driver->all();
    }
}