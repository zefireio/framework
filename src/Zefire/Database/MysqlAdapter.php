<?php

namespace Zefire\Database;

use Zefire\Contracts\DatabaseAdapter;

class MysqlAdapter implements DatabaseAdapter
{
    /**
     * Stores a count of conditions.
     *
     * @var int
     */
    protected $conditions = 0;
    /**
     * Generates a get sql mode SQL syntax.
     *
     * @return string
     */
    public function getSqlMode()
    {
        return "SELECT @@SESSION.sql_mode;";
    }
    /**
     * Generates a set sql mode SQL syntax.
     *
     * @param  array  $modes
     * @return string
     */
    public function setSqlMode(array $modes)
    {
        return "SET SESSION sql_mode='" . implode(',', $modes) . "';";
    }
    /**
     * Generates a show columns SQL syntax.
     *
     * @param  string $table
     * @return string
     */
    public function attributes(string $table)
    {
        return "SHOW COLUMNS FROM `" . $table . "`;";
    }
    /**
     * Generates an insert SQL syntax.
     *
     * @param  string $table
     * @param  array  $array
     * @return string
     */
    public function insert(string $table, array $array)
    {
        $fields = implode(', ', array_keys($array));
        $placeholders = implode(', :', array_keys($array));
        return "INSERT INTO `" . $table . "` (" . $fields . ") VALUES (:" . $placeholders . ");";
    }
    /**
     * Generates an update SQL syntax.
     *
     * @param  string $table
     * @param  array  $data
     * @param  array  $where
     * @param  array  $between
     * @return string
     */
    public function update(string $table, array $data, array $where = [], array $between = [])
    {
        $sets = '';
        foreach ($data as $key => $value) {
            $sets .= '`' . $key  . '`' . ' = :' . $key . ', ';
        }
        $sets = substr($sets, 0, -2);
        $between_condition = '';
        if (!empty($between)) {
            $between_condition .= ' WHERE `' . $between['field'] . '` BETWEEN :start_date AND :end_date';
        }
        $conditions = '';
        $count = (!empty($between)) ? 1 : 0;
        $max = count($where);
        if (!empty($where)) {
            foreach ($where as $key => $value) {
                if ($count == 0) {
                    $conditions .= ' WHERE `' . $key . '` ' . $value['operator'] . ' :' . $key;
                } else if ($count == 1) {
                    $conditions .= ' AND `' . $key . '` ' . $value['operator'] . ' :' . $key;
                }
                else {
                    $conditions .= $key . ' ' . $value['operator'] . ' :' . $key;                    
                }
                if ($count = $max) {
                    $conditions .= '';
                } else {
                    $conditions .= ' ' . $value['logic'] . ' ';
                }
                $count++;
            }
        }
        return 'UPDATE `' . $table . '` SET ' . $sets . $between_condition . $conditions;
    }
    /**
     * Generates a delete SQL syntax.
     *
     * @param  string $table
     * @param  array  $where
     * @param  array  $between
     * @return string
     */
    public function delete(string $table, array $where = [], array $between = [])
    {
        $between_condition = '';
        if (!empty($between)) {
            $between_condition .= ' WHERE `' . $between['field'] . '` BETWEEN :start_date AND :end_date';
        }
        $conditions = '';
        $count = (!empty($between)) ? 1 : 0;
        $max = count($where);
        if (!empty($where)) {
            foreach ($where as $key => $value) {
                if ($count == 0) {
                    $conditions .= ' WHERE `' . $key . '` ' . $value['operator'] . ' :' . $key;
                } else if ($count == 1) {
                    $conditions .= ' AND `' . $key . '` ' . $value['operator'] . ' :' . $key;
                }
                else {
                    $conditions .= $key . ' ' . $value['operator'] . ' :' . $key;                    
                }
                if ($count = $max) {
                    $conditions .= '';
                } else {
                    $conditions .= ' ' . $value['logic'] . ' ';
                }
                $count++;
            }
        }
        return 'DELETE FROM `' . $table . '` ' . $between_condition . $conditions;
    }
    /**
     * Generates a select SQL syntax based on primary key.
     *
     * @param  string $table
     * @return string
     */
    public function find(string $table)
    {
        return "SELECT * FROM `" . $table . "` WHERE `id` = :id";
    }
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
    public function query(
        string $table,
        array $select,
        array $distinct,
        array $join,
        array $where_in,
        array $where_not_in,
        array $where,
        array $between,
        array $not_between,
        array $group,
        array $having,
        array $order,
        $limit,
        $offset,
        $trashed
    )
    {
        $select = 'SELECT ' . implode(', ', $select);
        $distinct = (!empty($distinct)) ? ' DISTINCT `' . implode(', ', $distinct) . '`' : '';
        $join_condition = '';
        if (!empty($join)) {
            foreach ($join as $key => $value) {
                $join_condition .= ' ' . strtoupper($value['type']) . ' JOIN `' . $value['relatedTable'] . '` ON `' . $key . '`.`' . $value['primaryKey'] . '` = `' . $value['relatedTable'] . '`.`' . $value['foreignKey'] . '` ';    
            }            
        }
        $between_condition = '';
        if (!empty($between)) {
            $operator = ($this->conditions == 0) ? ' WHERE ' : ' AND ';
            $between_condition .= $operator . $between['field'] . ' BETWEEN :value1 AND :value2';
            $this->conditions++;
        }
        $not_between_condition = '';
        if (!empty($not_between)) {
            $operator = ($this->conditions == 0) ? ' WHERE ' : ' AND ';
            $not_between_condition .= $operator . $not_between['field'] . ' NOT BETWEEN :value1 AND :value2';
            $this->conditions++;
        }
        $where_in_condition = '';
        if (!empty($where_in)) {
            foreach ($where_in as $wi_condition) {
                $values = implode(', ', $wi_condition['array']);
                $operator = ($this->conditions == 0) ? ' WHERE ' : ' AND ';
                $where_in_condition .= $operator . $wi_condition['field'] . ' IN (' . $values . ')';
                $this->conditions++;    
            }            
        }
        $where_not_in_condition = '';
        if (!empty($where_not_in)) {
            foreach ($where_in as $wni_condition) {
                $values = implode(', ', $wni_condition['array']);
                $operator = ($this->conditions == 0) ? ' WHERE ' : ' AND ';
                $where_not_in_condition .= $operator . $wni_condition['field'] . ' IN (' . $values . ')';
                $this->conditions++;
            }
        }
        $conditions = '';
        $count = 0;
        $max = count($where);
        if (!empty($where)) {
            foreach ($where as $key => $value) {
                if ($this->conditions == 0) {
                    $conditions .= ' WHERE `' . $key . '` ' . $value['operator'] . ' :' . $key;
                } else if ($this->conditions == 1) {
                    $conditions .= ' AND `' . $key . '` ' . $value['operator'] . ' :' . $key;
                } else {
                    $conditions .= $key . ' ' . $value['operator'] . ' :' . $key;                    
                }
                if ($count = $max) {
                    $conditions .= '';
                } else {
                    $conditions .= ' ' . $value['logic'] . ' ';
                }
                $this->conditions++;
                $count++;
            }
        }
        $having_condition = '';
        if (!empty($having)) {
            $having_condition .= ' HAVING `' . $having['field'] . '` ' . $having['operator'] . ' :' . $having['field'];
        }
        $group_condition = '';
        if (!empty($group)) {
            $group_condition .= ' GROUP BY ' . implode(',', $group);
        }
        $order_condition = '';
        if (!empty($order)) {
            $order_condition .= ' ORDER BY `' . key($order) . '` ' . $order[key($order)];
        }
        $sql = $select . $distinct . ' FROM `' . $table . '` ' . $join_condition . $between_condition . $not_between_condition . $where_in_condition . $where_not_in_condition . $conditions;
        if ($trashed === false) {
            $sql .= ($this->conditions == 0) ? ' WHERE `deleted_at` IS NULL' : ' AND `deleted_at` IS NULL';
        }
        $sql .= $having_condition;
        $sql .= $group_condition;
        $sql .= $order_condition;
        if ($limit && $offset == null) {
            $sql .= ' LIMIT ' . $limit;
        }
        if ($offset && $limit) {
            $sql .= ' LIMIT ' . $limit . ' OFFSET ' . $offset;
        }
        return $sql;
    }
}