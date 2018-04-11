<?php

namespace Zefire\Session;

use Zefire\Contracts\Storable;
use Zefire\Helpers\Arr;
use Zefire\Core\Serializable;

class Session implements Storable
{
	use Serializable;
    /**
     * Stores the session driver instance.
     *
     * @var object
     */
	protected $driver;
	/**
     * Creates a new session instance.
     *
     * @return void
     */
	public function __construct()
    {
        $this->driver = \App::make(\App::config('session.driver'));
        if (!headers_sent()) {
            session_set_save_handler(
                array($this->driver, 'open'),
                array($this->driver, 'close'),
                array($this->driver, 'read'),
                array($this->driver, 'write'),
                array($this->driver, 'destroy'),
                array($this->driver, 'gc')
            );
            if ($this->driver instanceOf \Zefire\Session\FileSessionHandler) {
                session_save_path($this->driver->path());
            }    
            session_start();    
        }        
    }
    /**
     * Returns all content from session.
     *
     * @return array
     */
	public function all()
	{
		return $_SESSION;
	}
	/**
     * Gets a value from session.
     *
     * @param  string $key
     * @return mixed
     */
	public function get($key)
	{
		return Arr::get($key, $_SESSION);
	}
	/**
     * Checks if a value exists in session.
     *
     * @param  string $key
     * @return bool
     */
	public function exists($key)
	{
		return (Arr::get($key, $_SESSION) != null) ? true : false;		
	}
	/**
     * Sets a value to session.
     *
     * @param  string $key
     * @param  mixed  $value
     * @param  int    $ttl
     * @return mixed
     */
	public function set($key, $value, $ttl = 0)
	{
		return $_SESSION[$key] = $value;
	}
	/**
     * Deletes a value from session.
     *
     * @param  string $key
     * @return void
     */
	public function forget($key)
	{
		unset($_SESSION[$key]);
	}
	/**
     * Flushes session.
     *
     * @return void
     */
	public function flush()
	{
		session_unset();
	}	
}