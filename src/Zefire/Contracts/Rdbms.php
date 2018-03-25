<?php

namespace Zefire\Contracts;

interface Rdbms
{
    /**
     * Gets a PDO instance on unserialization.
     *
     * @return void
     */
    public function connect();
    /**
     * Gets SQL Mode for the current PDO instance.
     *
     * @return \stdClass
     */
    public function getSqlMode();
    /**
     * Sets SQL Modes on PDO instance.
     *
     * @param  array $modes
     * @return void
     */
    public function setSqlMode(array $modes = []);
    /**
     * Gets the desired SQL adapter.
     *
     * @return void
     */
    public function getAdapter();
    /**
     * Gets the list of attributes from a given table.
     *
     * @return \stdClass
     */
    public function attributes();
    /**
     * Gets the primary key name.
     *
     * @return string
     */
    public function getPrimaryKey();
    /**
     * Checks if a query has results.
     *
     * @return bool
     */
    public function hasResults();
    /**
     * Defines a select directive.
     *
     * @param  mixed $select
     * @return $this
     */
    public function select($select = ['*']);
    /**
     * Defines a distinct directive.
     *
     * @param  mixed $distinct
     * @return $this
     */
    public function distinct($distinct);
    /**
     * Defines a join directive.
     *
     * @param  string $table
     * @param  string $relatedTable
     * @param  string $primaryKey
     * @param  string $foreignKey
     * @param  string $type
     * @return $this
     */
    public function join(string $table, string $relatedTable, string $primaryKey, string $foreignKey, string $type = 'left');
    /**
     * Defines a where directive.
     *
     * @param  string $field
     * @param  string $operator
     * @param  mixed  $value
     * @param  string $logic
     * @return $this
     */
    public function where(string $field, string $operator, $value, string $logic = 'AND');
    /**
     * Defines a where or directive.
     *
     * @param  string $field
     * @param  string $operator
     * @param  mixed  $value
     * @return $this
     */
    public function whereOr(string $field, string $operator, $value);
    /**
     * Defines a where in directive.
     *
     * @param  string $field
     * @param  array  $array
     * @return $this
     */
    public function whereIn(string $field, array $array);
    /**
     * Defines a where not in directive.
     *
     * @param  string $field
     * @param  array  $array
     * @return $this
     */
    public function whereNotIn(string $field, array $array);
    /**
     * Defines a between directive.
     *
     * @param  string $field
     * @param  string $start_date
     * @param  string $end_date
     * @return $this
     */
    public function between(string $field, string $start_date, string $end_date);
    /**
     * Defines a group by in directive.
     *
     * @param  mixed  $groupBy
     * @return $this
     */
    public function groupBy($groupBy);
    /**
     * Defines a flag to retrieve trashed records.
     *
     * @return $this
     */
    public function withTrashed();
    /**
     * Defines an order by directive.
     *
     * @param  string $field
     * @param  string $order
     * @return $this
     */
    public function orderBy(string $field, string $order = 'asc');
    /**
     * Defines a limit directive.
     *
     * @param  string $limit
     * @return $this
     */
    public function limit($limit);
    /**
     * Defines an offset directive.
     *
     * @param  string $offset
     * @return $this
     */
    public function offset($offset);
    /**
     * Gets the last inserted ID.
     *
     * @return int
     */
    public function lastInsertId();
    /**
     * Inserts a new record and returns
     * the last inserted ID.
     *
     * @return int
     */
    public function insert(array $data);
    /**
     * Updates one or more records and returns
     * a count of affected rows.
     *
     * @return int
     */
    public function update(array $data);
    /**
     * Restores a soft deleted record by
     * setting the deleted_at column to null
     * and returns a count of affected rows.
     *
     * @return int
     */
    public function restore();
    /**
     * Soft deletes record by
     * setting the deleted_at column with a datetime
     * and returns a count of affected rows.
     *
     * @return int
     */
    public function delete();
    /**
     * Deletes record and returns a count of affected rows.
     *
     * @return int
     */
    public function forceDelete();
    /**
     * Finds a record by its primary key.
     *
     * @param  int $id
     * @return \stdClass
     */
    public function find(int $id);
    /**
     * Returns a count of records for a given query.
     *
     * @param  mixed $fields
     * @return int
     */
    public function count($fields = false);
    /**
     * Performs a max aggregate function and returns result
     *
     * @param  mixed $fields
     * @return int
     */
    public function max($fields = false);
    /**
     * Performs a min aggregate function and returns result
     *
     * @param  mixed $fields
     * @return int
     */
    public function min($fields = false);
    /**
     * Performs a avg aggregate function and returns result
     *
     * @param  mixed $fields
     * @return int
     */
    public function avg($fields = false);
    /**
     * Performs a sum aggregate function and returns result
     *
     * @param  mixed $fields
     * @return int
     */
    public function sum($fields = false);
    /**
     * Returns the first record for a given query.
     *
     * @return \stdClass
     */
    public function first();
    /**
     * Returns all records for a given query.
     *
     * @return array
     */
    public function get();
    /**
     * Starts a SQL transaction.
     *
     * @return void
     */
    public function beginTransaction();
    /**
     * Commits a SQL transaction.
     *
     * @return void
     */
    public function commit();
    /**
     * Rolls back a SQL transaction.
     *
     * @return void
     */
    public function rollback();
    /**
     * Performs a raw SQL query.
     *
     * @param  string $statement
     * @param  array  $bindings
     * @return mixed
     */
    public function raw(string $statement, array $bindings = []);
}