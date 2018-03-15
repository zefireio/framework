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
     * @return int
     */
    public function count();
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