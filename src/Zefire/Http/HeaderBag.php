<?php

namespace Zefire\Http;

use Zefire\Contracts\Storable;

class HeaderBag implements Storable
{
	/**
     * Stores header entries.
     *
     * @var array
     */
	protected $headers = [];
	/**
     * Sets a header in the header bag.
     *
     * @param  string $key
     * @param  string $value
     * @param  int    $ttl
     * @return void
     */
	public function set($key, $value, $ttl = 0)
	{
		$this->headers[$key] = $value;
	}
	/**
     * Checks if a entry exists in the header bag.
     *
     * @param  string $key
     * @return int
     */
	public function exists($key)
	{
		return (isset($this->headers[$key])) ? 1 : 0;
	}
	/**
     * Gets a header from the header bag.
     *
     * @param  string $key
     * @return mixed
     */
	public function get($key)
	{
		return (isset($this->headers[$key])) ? $this->headers[$key] : null;
	}
	/**
     * Gets all header entries from the header bag.
     *
     * @return array
     */
	public function all()
	{
		return $this->headers;
	}
	/**
     * Deletes a header entry from the header bag.
     *
     * @return void
     */
	public function forget($key)
	{
		unset($this->headers[$key]);
	}
	/**
     * Flushes the header bag.
     *
     * @return void
     */
	public function flush()
	{
		$this->headers = [];
	}
}