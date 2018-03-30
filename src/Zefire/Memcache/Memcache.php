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
        $this->memcache = new \Memcached('memcached_pool');
        $config = (\App::config('memcache'));
        if (isset($config['options']) && is_array($config['options']) && !empty($config['options'])) {
            foreach ($config['options'] as $key => $value) {
                if ($key == 'binary_protocol') {
                    if ($value === true) {
                        $this->memcache->setOption(\Memcached::OPT_BINARY_PROTOCOL, TRUE);    
                    } else {
                        $this->memcache->setOption(\Memcached::OPT_BINARY_PROTOCOL, FALSE);
                    }                    
                }
                if ($key == 'tcp_no_delay') {
                    if ($value === true) {
                        $this->memcache->setOption(\Memcached::OPT_TCP_NODELAY, TRUE);    
                    } else {
                        $this->memcache->setOption(\Memcached::OPT_TCP_NODELAY, FALSE);
                    }
                }
                if ($key == 'no_block') {
                    if ($value === true) {
                        $this->memcache->setOption(\Memcached::OPT_NO_BLOCK, TRUE);    
                    } else {
                        $this->memcache->setOption(\Memcached::OPT_NO_BLOCK, FALSE);
                    }
                }
                if ($key == 'connect_timeout') {
                    $this->memcache->setOption(\Memcached::OPT_CONNECT_TIMEOUT, $value);
                }
                if ($key == 'poll_timeout') {
                    $this->memcache->setOption(\Memcached::OPT_POLL_TIMEOUT, $value);
                }
                if ($key == 'recv_timeout') {
                    $this->memcache->setOption(\Memcached::OPT_RECV_TIMEOUT, $value);
                }
                if ($key == 'send_timeout') {
                    $this->memcache->setOption(\Memcached::OPT_SEND_TIMEOUT, $value);
                }
                if ($key == 'failover' && $value === true) {
                    $this->memcache->setOption(\Memcached::OPT_DISTRIBUTION, \Memcached::DISTRIBUTION_CONSISTENT);
                }
                if ($key == 'libketama_compatible') {
                    if ($value === true) {
                        $this->memcache->setOption(\Memcached::OPT_LIBKETAMA_COMPATIBLE, TRUE);    
                    } else {
                        $this->memcache->setOption(\Memcached::OPT_LIBKETAMA_COMPATIBLE, FALSE);
                    }
                }
                if ($key == 'retry_timeout') {
                    $this->memcache->setOption(\Memcached::OPT_RETRY_TIMEOUT, $value);
                }
                if ($key == 'server_failure_limit') {
                    $this->memcache->setOption(\Memcached::OPT_SERVER_FAILURE_LIMIT, $value);
                }
                if ($key == 'auto_eject_hosts') {
                    if ($value === true) {
                        $this->memcache->setOption(\Memcached::OPT_AUTO_EJECT_HOSTS, TRUE);    
                    } else {
                        $this->memcache->setOption(\Memcached::OPT_AUTO_EJECT_HOSTS, FALSE);
                    }
                }
                if ($key == 'auth') {
                    if (is_array($value)) {
                        if (isset($value['username']) && $value['username'] != '' && isset($value['password']) && $value['password'] != '') {
                            $this->memcache->setSaslAuthData($value['username'], $value['password']);
                        }
                    }
                }                
            }
        }
        if (!$this->memcache->getServerList()) {
            $servers = explode(',', $config['servers']);
            foreach ($servers as $server) {
                $parts = explode(':', $server);
                $this->memcache->addServer($parts[0], $parts[1]);
            }
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