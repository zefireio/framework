<?php

namespace Zefire\Database;

use Zefire\Database\Database;
use Zefire\Contracts\Query;

class DB extends Database implements Query
{
    /**
     * Stores the connection name.
     *
     * @var string
     */
    protected $connection;
    /**
     * Stores the table name.
     *
     * @var string
     */
    protected $table;
    /**
     * Sets the connection name and loads the adapter.
     *
     * @return $this
     */
    public function connection($connection)
    {
        $this->connection = $connection;
        $this->connect();
        $this->getAdapter();
        return $this;
    }
    /**
     * Sets the table name.
     *
     * @return $this
     */
    public function table(string $table)
    {
        $this->table = $table;
        return $this;
    }    
}