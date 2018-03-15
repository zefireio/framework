<?php

namespace Zefire\Contracts;

interface Query
{
    /**
     * Sets the connection name and loads the adapter.
     *
     * @return $this
     */
    public function connection($connection);
    /**
     * Sets the table name.
     *
     * @return $this
     */
    public function table(string $table);
}