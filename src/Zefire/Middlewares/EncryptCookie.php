<?php

namespace Zefire\Middlewares;

use Zefire\Contracts\Middleware;

class EncryptCookie implements Middleware
{
	/**
     * Stores a cookie bag instance.
     *
     * @var \Zefire\Http\CookieBag
     */
    protected $cookieBag;
    /**
     * Creates a new cookie encryption instance.
     *
     * @return void
     */
	public function __construct()
	{
		$this->cookieBag = \Cookie::all();        
	}
    /**
     * Handles cookie encryption.
     *
     * @return void
     */
	public function handle()
    {
        if (!empty($this->cookieBag)) {
        	foreach ($this->cookieBag as $key => $value) {
        		\Header::set('set-cookie', $this->make($key, $value));
        	}	
        }
    }
    /**
     * Builds an ecrypted cookie string.
     *
     * @param  string $key
     * @param  mixed  $value
     * @return string
     */
    protected function make($key, $value)
    {
    	if (preg_match("/[=,; \t\r\n\013\014]/", $key)) {
            throw new \Exception(sprintf('The cookie name "%s" contains invalid characters', $key));
        }
        if (empty($key)) {
            throw new \Exception('The cookie name cannot be empty');
        }
        $expire = time() + intval(\App::config('cookie.default_ttl'));
        $cookie = urlencode($key) . '=';
        $cookie .= rawurlencode(\Encryption::encrypt($value));
        $cookie .= '; expires=' . gmdate('D, d-M-Y H:i:s T', $expire) . '; max-age=' . $this->getMaxAge($expire);
        $cookie .= '; path=' . \App::config('cookie.path');
        $cookie .= '; domain=' . \App::config('app.host');
        if (\App::config('cookie.secure')) {
            $cookie .= '; secure';
        }
        if (\App::config('cookie.http_only')) {
            $cookie .= '; httponly';
        }
        $cookie .= '; samesite=' . \App::config('cookie.same_site');
        return $cookie;
    }
    /**
     * Gets the cookie max age.
     *
     * @param  int $expire
     * @return mixed
     */
    protected function getMaxAge($expire)
    {
        return ($expire != 0) ? $expire - time() : 0;
    }
    /**
     * Checks if a cookie has expired.
     *
     * @param  int $expire
     * @return bool
     */
    protected function expired($expire)
    {
        return ($expire < time()) ? true : false;
    }
}