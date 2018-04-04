<?php

namespace Zefire\Database;

use Zefire\Contracts\Rdbms;
use Zefire\Event\Dispatcher;
use \PDO;

abstract class Database implements Rdbms
{
    /**
     * Stores select directives.
     *
     * @var array
     */
    protected $select = ['*'];
    /**
     * Stores distinct directives.
     *
     * @var array
     */
    protected $distinct = [];
    /**
     * Stores join directives.
     *
     * @var array
     */
    protected $join = [];
    /**
     * Stores where directives.
     *
     * @var array
     */
    protected $where = [];
    /**
     * Stores where in directives.
     *
     * @var array
     */
    protected $in = [];
    /**
     * Stores where not in directives.
     *
     * @var array
     */
    protected $notIn = [];
    /**
     * Stores between directives.
     *
     * @var array
     */
    protected $between = [];
    /**
     * Stores not between directives.
     *
     * @var array
     */
    protected $notBetween = [];
    /**
     * Stores group by directives.
     *
     * @var array
     */
    protected $groupBy = [];
    /**
     * Stores having directives.
     *
     * @var array
     */
    protected $having = [];
    /**
     * Stores the with trashed flag.
     *
     * @var bool
     */
    protected $withTrashed = false;
    /**
     * Stores order by directives.
     *
     * @var array
     */
    protected $orderBy = [];
    /**
     * Stores a limit directive.
     *
     * @var mixed
     */
    protected $limit = null;
    /**
     * Stores an offset directive.
     *
     * @var mixed
     */
    protected $offset = null;
    /**
     * Stores the PDO statment.
     *
     * @var \PDOStatement
     */
    protected $statement;
    /**
     * Stores bindings.
     *
     * @var array
     */
    protected $bindings = [];
    /**
     * Stores an adapter instance.
     *
     * @var object
     */
    protected $adapter;
    /**
     * Stores a PDO instance.
     *
     * @var \PDO
     */
    protected $pdo;
    /**
     * Stores a dispatcher instance.
     *
     * @var \Zefire\Event\Dispatcher
     */
    protected $dispatcher;    
    /**
     * Gets a PDO instance on unserialization.
     *
     * @return void
     */
    public function connect()
    {
        $this->dispatcher = \App::make(Dispatcher::class);
        $this->pdo = $this->resolve($this->connection);        
    }
    /**
     * Gets SQL Mode for the current PDO instance.
     *
     * @return \stdClass
     */
    public function getSqlMode()
    {
        $this->prepare($this->adapter->getSqlMode());
        $this->statement->execute();
        return $this->statement->fetch(PDO::FETCH_OBJ);
    }
    /**
     * Sets SQL Modes on PDO instance.
     *
     * @param  array $modes
     * @return void
     */
    public function setSqlMode(array $modes = [])
    {
        $this->prepare($this->adapter->setSqlMode($modes));
        $this->statement->execute();        
    }
    /**
     * Gets the desired SQL adapter.
     *
     * @return void
     */
    public function getAdapter()
    {
        $this->adapter = \App::make(\App::config('database.' . $this->connection . '.adapter'));        
    }
    /**
     * Gets the list of attributes from a given table.
     *
     * @return \stdClass
     */
    public function attributes()
    {
        $this->prepare($this->adapter->attributes($this->table));
        $this->statement->execute();
        return $this->statement->fetchAll(PDO::FETCH_OBJ);
    }
    /**
     * Gets the primary key name.
     *
     * @return string
     */
    public function getPrimaryKey()
    {
        $primaryKey = null;
        $rows = $this->attributes();
        foreach ($rows as $row) {
            if (isset($row->Key) && $row->Key == 'PRI') {
                $primaryKey = $row->Field;
            }
        }
        return $primaryKey;
    }
    /**
     * Checks if a query has results.
     *
     * @return bool
     */
    public function hasResults()
    {
        return ($this->statement->rowCount() > 0) ? true : false;
    }
    /**
     * Defines a select directive.
     *
     * @param  mixed $select
     * @return $this
     */
    public function select($select = ['*'])
    {
        $this->select = $this->stringToArray($select);
        return $this;
    }
    /**
     * Defines a distinct directive.
     *
     * @param  mixed $distinct
     * @return $this
     */
    public function distinct($distinct)
    {
        $this->distinct = $this->stringToArray($distinct);
        return $this;
    }
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
    public function join(string $table, string $relatedTable, string $primaryKey, string $foreignKey, string $type = 'left')
    {
        $this->join[$table] = [
            'primaryKey'    => $primaryKey,
            'foreignKey'    => $foreignKey,
            'relatedTable'  => $relatedTable,
            'type'          => $type
        ];
        return $this;
    }
    /**
     * Defines a where directive.
     *
     * @param  string $field
     * @param  string $operator
     * @param  mixed  $value
     * @param  string $logic
     * @return $this
     */
    public function where(string $field, string $operator, $value, string $logic = 'AND')
    {
        $this->where[$field] = [
            'operator'  => $operator,
            'value'     => $value,
            'logic'     => $logic
        ];
        return $this;
    }
    /**
     * Defines a where or directive.
     *
     * @param  string $field
     * @param  string $operator
     * @param  mixed  $value
     * @return $this
     */
    public function whereOr(string $field, string $operator, $value)
    {
        $this->where($field, $operator, $value, 'OR');
        return $this;
    }
    /**
     * Defines a where in directive.
     *
     * @param  string $field
     * @param  array  $array
     * @return $this
     */
    public function in(string $field, array $array)
    {
        $this->in[] = [
            'field'     => $field,
            'operator'  => 'IN',
            'array'     => $array
        ];
        return $this;
    }
    /**
     * Defines a where not in directive.
     *
     * @param  string $field
     * @param  array  $array
     * @return $this
     */
    public function notIn(string $field, array $array)
    {
        $this->notIn[] = [
            'field'     => $field,
            'operator'  => 'NOT IN',
            'array'     => $array
        ];
        return $this;
    }
    /**
     * Defines a between directive.
     *
     * @param  string $field
     * @param  string $value1
     * @param  string $value2
     * @return $this
     */
    public function between(string $field, string $value1, string $value2)
    {
        $this->between = ['field' => $field, 'value1' => $value1, 'value2' => $value2];
        return $this;
    }
    /**
     * Defines a not between directive.
     *
     * @param  string $field
     * @param  string $value1
     * @param  string $value2
     * @return $this
     */
    public function notBetween(string $field, string $value1, string $value2)
    {
        $this->notBetween = ['field' => $field, 'value1' => $value1, 'value2' => $value2];
        return $this;
    }
    /**
     * Defines a group by in directive.
     *
     * @param  mixed  $groupBy
     * @return $this
     */
    public function groupBy($groupBy)
    {
        $this->groupBy = $this->stringToArray($groupBy);
        return $this;
    }
    /**
     * Defines a having directive.
     *
     * @param  string $field
     * @param  string $operator
     * @param  mixed  $value
     * @return $this
     */
    public function having(string $field, string $operator, $value)
    {
        $this->having = [
            'field'     => $field,
            'operator'  => $operator,
            'value'     => $value
        ];
        return $this;
    }
    /**
     * Defines a flag to retrieve trashed records.
     *
     * @return $this
     */
    public function withTrashed()
    {
        $this->withTrashed = true;
        return $this;
    }
    /**
     * Defines an order by directive.
     *
     * @param  string $field
     * @param  string $order
     * @return $this
     */
    public function orderBy(string $field, string $order = 'asc')
    {
        $this->orderBy = [$field => $order];
        return $this;
    }
    /**
     * Defines a limit directive.
     *
     * @param  string $limit
     * @return $this
     */
    public function limit($limit)
    {
        $this->limit = $limit;
        return $this;
    }
    /**
     * Defines an offset directive.
     *
     * @param  string $offset
     * @return $this
     */
    public function offset($offset)
    {
        $this->offset = $offset;
        return $this;
    }
    /**
     * Gets the last inserted ID.
     *
     * @return int
     */
    public function lastInsertId()
    {
       return $this->pdo->lastInsertId();
    }
    /**
     * Inserts a new record and returns
     * the last inserted ID.
     *
     * @return int
     */
    public function insert(array $data)
    {
        $data = $this->created($data);
        $this->prepare($this->adapter->insert($this->table, $data));
        foreach ($data as $key => $value) {
            $this->bind($key, $value);
        }
        $this->statement->execute();
        return $this->lastInsertId();
    }
    /**
     * Updates one or more records and returns
     * a count of affected rows.
     *
     * @return int
     */
    public function update(array $data)
    {
        $data = $this->updated($data);
        $this->prepare($this->adapter->update($this->table, $data, $this->where, $this->between));
        if (!empty($this->between)) {
            $this->bind('start_date', $this->between['start_date']);
            $this->bind('end_date', $this->between['end_date']);
        }
        if (!empty($this->where)) {
            foreach ($this->where as $key => $value) {
                $this->bind($key, $value['value']);
            }
        }
        foreach ($data as $key => $value) {
            $this->bind($key, $value);
        }
        $this->statement->execute();
        return $this->statement->rowCount();
    }
    /**
     * Restores a soft deleted record by
     * setting the deleted_at column to null
     * and returns a count of affected rows.
     *
     * @return int
     */
    public function restore()
    {
        $data = $this->restored([]);
        $this->prepare($this->adapter->update($this->table, $data, $this->where, $this->between));
        if (!empty($this->between)) {
            $this->bind('start_date', $this->between['start_date']);
            $this->bind('end_date', $this->between['end_date']);
        }
        if (!empty($this->where)) {
            foreach ($this->where as $key => $value) {
                $this->bind($key, $value['value']);
            }
        }
        foreach ($data as $key => $value) {
            $this->bind($key, $value);
        }
        $this->statement->execute();
        return $this->statement->rowCount();
    }
    /**
     * Soft deletes record by
     * setting the deleted_at column with a datetime
     * and returns a count of affected rows.
     *
     * @return int
     */
    public function delete()
    {
        $data = $this->deleted([]);
        $this->prepare($this->adapter->update($this->table, $data, $this->where, $this->between));
        if (!empty($this->between)) {
            $this->bind('start_date', $this->between['start_date']);
            $this->bind('end_date', $this->between['end_date']);
        }
        if (!empty($this->where)) {
            foreach ($this->where as $key => $value) {
                $this->bind($key, $value['value']);
            }
        }
        foreach ($data as $key => $value) {
            $this->bind($key, $value);
        }
        $this->statement->execute();
        return $this->statement->rowCount();
    }
    /**
     * Deletes record and returns a count of affected rows.
     *
     * @return int
     */
    public function forceDelete()
    {
        $this->prepare($this->adapter->delete($this->table, $this->where, $this->between));
        if (!empty($this->between)) {
            $this->bind('start_date', $this->between['start_date']);
            $this->bind('end_date', $this->between['end_date']);
        }
        if (!empty($this->where)) {
            foreach ($this->where as $key => $value) {
                $this->bind($key, $value['value']);
            }
        }
        $this->statement->execute();
        return $this->statement->rowCount();
    }
    /**
     * Finds a record by its primary key.
     *
     * @param  int $id
     * @return \stdClass
     */
    public function find(int $id)
    {
        $this->prepare($this->adapter->find($this->table));
        $this->bind('id', $id);
        $this->statement->execute();
        return $this->statement->fetch(PDO::FETCH_OBJ);
    }
    /**
     * Returns a count of records for a given query.
     *
     * @param  mixed $fields
     * @return int
     */
    public function count($fields = false)
    {
        if ($fields === false) {
            $this->select = ['count(*) as count'];    
        } else {
            $this->select = $this->stringToArray($fields, 'count');    
        }        
        $this->query();
        $res = $this->statement->fetch(PDO::FETCH_OBJ);
        return $res->count;
    }
    /**
     * Performs a max aggregate function and returns result
     *
     * @param  mixed $fields
     * @return int
     */
    public function max($fields = false)
    {
        if ($fields === false) {
            $this->select = ['count(*) as max'];    
        } else {
            $this->select = $this->stringToArray($fields, 'max');    
        }        
        $this->query();
        $res = $this->statement->fetch(PDO::FETCH_OBJ);
        return $res->max;
    }
    /**
     * Performs a min aggregate function and returns result
     *
     * @param  mixed $fields
     * @return int
     */
    public function min($fields = false)
    {
        if ($fields === false) {
            $this->select = ['count(*) as min'];    
        } else {
            $this->select = $this->stringToArray($fields, 'min');    
        }        
        $this->query();
        $res = $this->statement->fetch(PDO::FETCH_OBJ);
        return $res->min;
    }
    /**
     * Performs a avg aggregate function and returns result
     *
     * @param  mixed $fields
     * @return int
     */
    public function avg($fields = false)
    {
        if ($fields === false) {
            $this->select = ['count(*) as avg'];    
        } else {
            $this->select = $this->stringToArray($fields, 'avg');    
        }        
        $this->query();
        $res = $this->statement->fetch(PDO::FETCH_OBJ);
        return $res->avg;
    }
    /**
     * Performs a sum aggregate function and returns result
     *
     * @param  mixed $fields
     * @return int
     */
    public function sum($fields = false)
    {
        if ($fields === false) {
            $this->select = ['count(*) as sum'];    
        } else {
            $this->select = $this->stringToArray($fields, 'sum');    
        }        
        $this->query();
        $res = $this->statement->fetch(PDO::FETCH_OBJ);
        return $res->sum;
    }
    /**
     * Returns the first record for a given query.
     *
     * @return \stdClass
     */
    public function first()
    {
        $this->query();
        return $this->statement->fetch(PDO::FETCH_OBJ);
    }
    /**
     * Returns all records for a given query.
     *
     * @return array
     */
    public function get()
    {
        $this->query();
        return $this->statement->fetchAll(PDO::FETCH_OBJ);
    }
    /**
     * Starts a SQL transaction.
     *
     * @return void
     */
    public function beginTransaction()
    {
        return $this->pdo->beginTransaction();
    }
    /**
     * Commits a SQL transaction.
     *
     * @return void
     */
    public function commit()
    {
        return $this->pdo->commit();
    }
    /**
     * Rolls back a SQL transaction.
     *
     * @return void
     */
    public function rollback()
    {
        return $this->pdo->rollback();
    }
    /**
     * Performs a raw SQL query.
     *
     * @param  string $statement
     * @param  array  $bindings
     * @return mixed
     */
    public function raw(string $statement, array $bindings = [])
    {
        if (stristr($statement, 'insert')) {
            $case = 'lastInsertId';
        } else if (stristr($statement, 'select')) {
            $case = 'all';
        }  else if (stristr($statement, 'show')) {
            $case = 'all';
        } else {
            $case = 'rowCount';
        }
        $this->prepare($statement);
        if (!empty($bindings)) {
            foreach ($bindings as $key => $value) {
                $this->bind($key, $value);
            }    
        }
        $this->statement->execute();
        switch ($case) {
            case 'lastInsertId':
                return $this->lastInsertId();
            case 'all':
                return $this->statement->fetchAll(PDO::FETCH_OBJ);
            default:
                return $this->statement->rowCount();
        }
    }
    /**
     * Converts a string of fields to array if needed.
     *
     * @param  mixed  $data
     * @return array
     */
    protected function stringToArray($data, $aggregate = false)
    {
        if (!is_array($data)) {
            $explode = explode('|', $data);
            $data = $explode;
        }
        if ($aggregate !== false) {
            $aggregated_string = $aggregate . '(' . implode(',', $data);
            $aggregated_string = substr($aggregated_string, 0, -1);
            $aggregated_string .= ')';
            return [$aggregated_string];
        } else {
            return $data;    
        }        
    }
    /**
     * Performs a SQL query based on registered directives.
     *
     * @return mixed
     */
    protected function query()
    {
        $this->prepare($this->adapter->query(
            $this->table,
            $this->select,
            $this->distinct,
            $this->join,
            $this->in,
            $this->notIn,
            $this->where,
            $this->between,
            $this->notBetween,
            $this->groupBy,
            $this->having,
            $this->orderBy,
            $this->limit,
            $this->offset,
            $this->withTrashed
        ));
        if (!empty($this->between)) {
            $this->bind('value1', $this->between['value1']);
            $this->bind('value2', $this->between['value2']);
        }
        if (!empty($this->notBetween)) {
            $this->bind('value1', $this->notBetween['value1']);
            $this->bind('value2', $this->notBetween['value2']);
        }
        if (!empty($this->where)) {
            foreach ($this->where as $key => $value) {
                $this->bind($key, $value['value']);
            }
        }
        if (!empty($this->having)) {
            $this->bind($this->having['field'], $this->having['value']);            
        }
        if (\App::config('database.' . $this->connection . '.strict')) {
            $this->setSqlMode(\App::config('database.' . $this->connection . '.strict'));
        }
        $this->statement->execute();
    }
    /**
     * Prepares a SQL statement.
     *
     * @param  string $statement
     * @return void
     */
    protected function prepare(string $statement)
    {
        if ($this->pdo == null) {
            throw new \Exception('Failed to connect to database, please check your database connection settings.', 500);
        }
        $this->statement = $this->pdo->prepare($statement);
    }
    /**
     * Binds values to a SQL statement.
     *
     * @param  string $param
     * @param  mixed  $value
     * @param  string $type
     * @return void
     */
    protected function bind(string $param, $value, $type = null)
    {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        $this->statement->bindValue($param, $value, $type);
        $this->bindings[$param] = $value;
    }
    /**
     * Resolves a connection and returns a PDO instance.
     *
     * @param  string $connectionName
     * @return \PDO
     */
    protected function resolve($connectionName)
    {
        $connection = \App::config('database.' . $connectionName);
        if ($connection != null) {
            if ($connection['type'] == null || $connection['type'] == '') {
                return null;
            }
            switch ($connection['type']) {
                case 'mysql':
                    if (isset($connection['modes']) && !empty($connection['modes'])) {
                        $sqlModes = implode(",", $connection['modes']);
                    } else if (isset($connection['strict']) && $connection['strict'] === true) {
                        $sqlModes = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';                    
                    } else {
                        $sqlModes = 'NO_ENGINE_SUBSTITUTION';
                    }
                    if ($connection['host'] == '' || $connection['port'] == '' || $connection['database'] == '') {
                        return null;
                    }
                    $dsn = 'mysql:host=' . $connection['host'] . ';port=' . $connection['port'] . ';dbname=' . $connection['database'];
                    $pdo = new PDO(
                        $dsn,
                        $connection['username'],
                        $connection['password'],
                        [
                            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                            PDO::MYSQL_ATTR_INIT_COMMAND => "SET SESSION sql_mode='" . $sqlModes . "'"
                        ]
                    );
                    $this->dispatcher->now(
                        'db-connect',
                        [
                            'status' => ($pdo) ? 'Success:' : 'Failure:',
                            'dsn' => $dsn,
                            'connection' => $connectionName
                        ]
                    );
                    break;
                case 'pgsql':
                    if ($connection['host'] == '' || $connection['port'] == '' || $connection['database'] == '') {
                        return null;
                    }
                    $dsn = 'pgsql:host=' . $connection['host'] . ';port=' . $connection['port'] . ';dbname=' . $connection['database'];
                    $pdo = new PDO(
                        $dsn,
                        $connection['username'],
                        $connection['password']
                    );
                    $this->dispatcher->now(
                        'db-connect',
                        [
                            'status' => ($pdo) ? 'Success:' : 'Failure:',
                            'dsn' => $dsn,
                            'connection' => $connectionName
                        ]
                    );
                    break;
                case 'sqlite':
                    $dsn = 'sqlite::memory:';
                    $pdo = new PDO(
                        $dsn,
                        null,
                        null,
                        [
                            PDO::ATTR_PERSISTENT => true
                        ]
                    );
                    $this->dispatcher->now(
                        'db-connect',
                        [
                            'status' => ($pdo) ? 'Success:' : 'Failure:',
                            'dsn' => $dsn,
                            'connection' => $connectionName
                        ]
                    );
                    break;
            }
            return $pdo;    
        } else {
            return null;
        }
    }
    /**
     * Adds a created timestamp and user id.
     *
     * @param  array $array
     * @return array
     */
    protected function created($array)
    {
        $data = [
           'created_at' => date('Y-m-d H:i:s'),
           // 'created_by' => \Session::get('user.id'),
           'updated_at' => date('Y-m-d H:i:s'),
           // 'updated_by' => \Session::get('user.id')
        ];
        return array_merge($array, $data);
    }
    /**
     * Adds an updated timestamp and user id.
     *
     * @param  array $array
     * @return array
     */
    protected function updated($array)
    {
        $data = [
           'updated_at' => date('Y-m-d H:i:s'),
           // 'updated_by' => \Session::get('user.id'),
        ];
        return array_merge($array, $data);
    }
    /**
     * Adds a deleted timestamp and user id.
     *
     * @param  array $array
     * @return array
     */
    protected function deleted($array)
    {
        $data = [
           'deleted_at' => date('Y-m-d H:i:s'),
           // 'deleted_by' => \Session::get('user.id'),
        ];
        return array_merge($array, $data);
    }
    /**
     * Adds an updated timestamp and user id
     * and removes the deleted timestamp and user id.
     *
     * @param  array $array
     * @return array
     */
    protected function restored($array)
    {
        $data = [
           'updated_at' => date('Y-m-d H:i:s'),
           // 'updated_by' => \Session::get('user.id'),
           'deleted_at' => null,
           'deleted_by' => null,
        ];
        return array_merge($array, $data);
    }
}