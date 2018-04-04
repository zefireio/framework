<?php

namespace Zefire\Session;

// use Zefire\Core\Serializable;
// use Zefire\Contracts\Connectable;

class DatabaseSessionHandler implements \SessionHandlerInterface
{
	// use Serializable;
    /**
     * Stores a DB instance.
     *
     * @var \Zefire\Database\DB
     */
    protected $db;
    /**
     * Creates a new database session handler instance.
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
        $this->db = \App::make('Zefire\Database\DB');
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
        $session = $this->db->connection('mysql1')->table('session')->where('id', '=', $sessionId)->first();
        if (!isset($session->data)) {
            $this->db->insert(['id' => $sessionId, 'data' => '']);
            $session = $this->db->where('id', '=', $sessionId)->first();
        }
        return $session->data;
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
        $this->db->where('id', '=', $sessionId)->update(['data' => $data]);
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
        $this->db->where('id', '=', $sessionId)->delete();
    }
    /**
     * Garbage collection session save handler callback.
     *
     * @param  int $lifetime
     * @return void
     */
    public function gc($lifetime)
    {
        $this->db->connection('mysql1')->raw("DELETE FROM session WHERE updated_at < DATE_SUB(NOW(), INTERVAL :ttl SECOND)", ['ttl' => \App::config('session.life')]);
    }
}