<?php

namespace Zefire\Contracts;

interface Modelisable
{
    /**
     * Sets a model's attribute.
     *
     * @param  string $key
     * @param  mixed  $value
     * @return void
     */
    public function __set(string $key, $value);
    /**
     * Gets a model's attribute.
     *
     * @param  string $key
     * @return void
     */
    public function __get(string $key);
    /**
     * Creates a new record.
     *
     * @param  array $array
     * @return object
     */
    public function create(array $array);
    /**
     * Saves a current active record.
     *
     * @return object
     */
    public function save();
    /**
     * Soft deletes a record.
     *
     * @return int
     */
    public function delete();
    /**
     * Restores a record.
     *
     * @return int
     */
    public function restore();
    /**
     * Deletes a record.
     *
     * @return int
     */
    public function forceDelete();
    /**
     * Finds a record by primary key.
     *
     * @param  int $id
     * @return object
     */
    public function find(int $id);
    /**
     * Executes a count(*) query and returns count.
     *
     * @param  mixed $fields
     * @return int
     */
    public function count($fields = false);
    /**
     * Executes a max(*) query and returns max.
     *
     * @param  mixed $fields
     * @return int
     */
    public function max($fields = false);
    /**
     * Executes a min(*) query and returns min.
     *
     * @param  mixed $fields
     * @return int
     */
    public function min($fields = false);
    /**
     * Executes an avg(*) query and returns avg.
     *
     * @param  mixed $fields
     * @return int
     */
    public function avg($fields = false);
    /**
     * Executes a sum(*) query and returns sum.
     *
     * @param  mixed $fields
     * @return int
     */
    public function sum($fields = false);
    /**
     * Retrieves first record.
     *
     * @return object
     */
    public function first();
    /**
     * Retrieves all records.
     *
     * @return \Zefire\Database\Collection
     */
    public function get();
    /**
     * Paginates a record set.
     *
     * @param  int $perPage
     * @return \Zefire\Database\Collection
     */
    public function paginate(int $perPage);
    /**
     * Defines which relations should be pulled with records.
     *
     * @param  mixed $relations
     * @return object
     */
    public function with($relations);
    /**
     * Performs a one to one relationship.
     *
     * @param  string $related
     * @param  string $foreignKey
     * @return mixed
     */
    public function hasOne(string $related, string $foreignKey);
    /**
     * Performs a one to many relationship.
     *
     * @param  string $related
     * @param  string $foreignKey
     * @return mixed
     */
    public function hasMany(string $related, string $foreignKey);
    /**
     * Performs a many to many relationship.
     *
     * @param  string $related
     * @param  string $pivot
     * @param  string $ownerKey
     * @param  string $foreignKey
     * @return mixed
     */
    public function manyToMany(string $related, string $pivot, string $ownerKey, string $foreignKey);
    /**
     * Gets related records for a relation.
     *
     * @return object
     */
    public function loadRelations();
    /**
     * Converts record to an array of attributes.
     *
     * @return array
     */
    public function toArray();
}