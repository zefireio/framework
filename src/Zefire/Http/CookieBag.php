<?php

namespace Zefire\Http;

use Zefire\Contracts\Storable;
use Zefire\Encryption\Encryption;

class CookieBag implements Storable
{
	/**
     * Stores an encryption instance.
     *
     * @var \Zefire\Encryption\Encryption
     */
	protected $encryption;
	/**
     * Stores cookie entries.
     *
     * @var array
     */
	protected $cookies = [];
	/**
     * Creates a new cookie bag instance.
     *
     * @param  \Zefire\Encryption\Encryption
     * @return void
     */
	public function __construct(Encryption $encryption)
	{
		$this->encryption = $encryption;
		foreach ($_COOKIE as $key => $value) {
			$this->cookies[$key] = $this->encryption->decrypt($value);	
		}
	}
	/**
     * Sets a cookie in the cookie bag.
     *
     * @param  string $key
     * @param  mixed  $value
     * @param  int    $ttl
     * @return void
     */
	public function set($key, $value, $ttl = 0)
	{
		$this->cookies[$key] = $value;
	}
	/**
     * Checks if a entry exists in the cookie bag.
     *
     * @param  string $key
     * @return int
     */
	public function exists($key)
	{
		return (isset($this->cookies[$key])) ? 1 : 0;
	}
	/**
     * Gets a cookie from the cookie bag.
     *
     * @param  string $key
     * @return mixed
     */
	public function get($key)
	{
		return (isset($this->cookies[$key])) ? $this->cookies[$key] : null;
	}
	/**
     * Gets all cookie entries from the cookie bag.
     *
     * @return array
     */
	public function all()
	{
		return $this->cookies;
	}
	/**
     * Deletes a cookie entry from the cookie bag.
     *
     * @return void
     */
	public function forget($key)
	{
		unset($this->cookies[$key]);
	}
	/**
     * Flushes the cookie bag.
     *
     * @return void
     */
	public function flush()
	{
		$this->cookies = [];
	}
}