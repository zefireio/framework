<?php

namespace Zefire\Contracts;

interface DatabaseAdapter
{
    /**
     * Generates a show columns SQL syntax.
     *
     * @param  string $table
     * @return string
     */
    public function attributes(string $table);
    /**
     * Generates an insert SQL syntax.
     *
     * @param  string $table
     * @param  array  $array
     * @return string
     */
    public function insert(string $table, array $array);
    /**
     * Generates an update SQL syntax.
     *
     * @param  string $table
     * @param  array  $data
     * @param  array  $where
     * @param  array  $between
     * @return string
     */
    public function update(string $table, array $data, array $where = [], array $between = []);
    /**
     * Generates a delete SQL syntax.
     *
     * @param  string $table
     * @param  array  $where
     * @param  array  $between
     * @return string
     */
    public function delete(string $table, array $where = [], array $between = []);
    /**
     * Generates a select SQL syntax based on primary key.
     *
     * @param  string $table
     * @return string
     */
    public function find(string $table);
    /**
     * Generates a query SQL syntax.
     *
     * @param  string $table
     * @param  array  $select
     * @param  array  $distinct
     * @param  array  $join
     * @param  array  $where_in
     * @param  array  $where_not_in
     * @param  array  $where
     * @param  array  $between
     * @param  array  $not_between
     * @param  array  $group
     * @param  array  $having
     * @param  array  $order
     * @param  mixed  $limit
     * @param  mixed  $offset
     * @param  bool   $trashed
     * @return string
     */
    public function query(string $table, array $select, array $distinct, array $join, array $where_in, array $where_not_in, array $where, array $between, array $not_between, array $group, array $having, array $order, $limit, $offset, $trashed);
}