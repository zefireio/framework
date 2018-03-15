<?php

namespace Zefire\Contracts;

interface Storable
{
    /**
     * Sets an entry in a Key/Value pair store.
     *
     * @param  string $key
     * @param  mixed  $value
     * @param  int    $ttl
     * @return mixed
     */
    public function set($key, $value, $ttl = 0);
    /**
     * Checks if an entry exists in a Key/Value pair store.
     *
     * @param  string $key
     * @return bool
     */
    public function exists($key);
    /**
     * Gets an entry from a Key/Value pair store.
     *
     * @param  string $key
     * @return mixed
     */
    public function get($key);
    /**
     * Deletes an entry from a Key/Value pair store.
     *
     * @param  string $key
     * @return void
     */
    public function forget($key);
    /**
     * Flushes a Key/Value pair store.
     *
     * @return void
     */
    public function flush();
    /**
     * Gets all entries from a Key/Value pair store.
     *
     * @return void
     */
    public function all();
}