<?php

namespace Zefire\Session;

use Zefire\Core\Serializable;
use Zefire\Contracts\Connectable;

class DatabaseSessionHandler implements \SessionHandlerInterface, Connectable
{
	use Serializable;
    /**
     * Stores a connection name.
     *
     * @var string
     */
    protected $connection;
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
        $this->connection = \App::config('session.connection');
        $this->connect($this->connection);
        $this->checkSessionTable();
    }
    /**
     * Connect to db server.
     *
     * @param  string $connection
     * @return void
     */
    public function connect($connection)
    {
        $this->db = \App::make('Zefire\Database\DB');
        $this->db->connection($connection);        
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
        $session = $this->db->table('session')
            ->where('id', '=', $sessionId)
            ->first();
        if (!isset($session->data)) {
            $this->db->table('session')
                ->insert(['id' => $sessionId, 'data' => '']);
            $session = $this->db->connection($this->connection)
                ->table('session')
                ->where('id', '=', $sessionId)
                ->first();
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
        $this->db->table('session')
            ->where('id', '=', $sessionId)
            ->update(['data' => $data]);
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
        $this->db->table('session')
            ->where('id', '=', $sessionId)
            ->delete();
    }
    /**
     * Garbage collection session save handler callback.
     *
     * @param  int $lifetime
     * @return void
     */
    public function gc($lifetime)
    {
        $this->db->raw("DELETE FROM session WHERE updated_at < DATE_SUB(NOW(), INTERVAL :ttl SECOND)", ['ttl' => \App::config('session.life')]);
    }
    /**
     * Checks if the session table exists
     * and will create it if needed.
     *
     * @return void
     */
    protected function checkSessionTable()
    {
        $this->db->raw("CREATE TABLE IF NOT EXISTS `session` (
            `id` char(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            `deleted_at` timestamp NULL DEFAULT NULL,
            `created_by` int(11) DEFAULT NULL,
            `updated_by` int(11) DEFAULT NULL,
            `deleted_by` int(11) DEFAULT NULL,
            `data` longtext COLLATE utf8_unicode_ci,
            PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;"
        );
    }
}