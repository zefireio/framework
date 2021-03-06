<?php

namespace Zefire\Database;

use Zefire\Database\Database;
use Zefire\Contracts\Modelisable;
use Zefire\Database\Collection;
use Zefire\Core\Serializable;

abstract class Model extends Database implements Modelisable
{
    use Serializable;
    /**
     * Stores the table's primary key.
     *
     * @var string
     */
    protected $primaryKey;
    /**
     * Stores a list of model attributes.
     *
     * @var array
     */
    protected $attributes = [];
    /**
     * Flags a model to indicate if query returned results.
     *
     * @var bool
     */
    protected $hasResults = false;
    /**
     * Stores a list of relations.
     *
     * @var array
     */
    protected $with = [];
    /**
     * Stores a pivot table.
     *
     * @var object
     */
    protected $pivot;
    /**
     * Creates a new model instance,
     * gets the proper adapter and the primary key.
     *
     * @return void
     */
    public function __construct()
    {
        $this->connect();
        $this->getAdapter();
        $this->primaryKey = $this->getPrimaryKey();
    }    
    /**
     * Sets a model's attribute.
     *
     * @param  string $key
     * @param  mixed  $value
     * @return void
     */
    public function __set(string $key, $value)
    {
        $this->attributes[$key] = $value;
    }
    /**
     * Gets a model's attribute.
     *
     * @param  string $key
     * @return void
     */
    public function __get(string $key)
    {
        return $this->attributes[$key];
    }
    /**
     * Creates a new record.
     *
     * @param  array $array
     * @return object
     */
    public function create(array $array)
    {
        $lastInsertId = parent::insert($array);
        return $this->find($lastInsertId);
    }
    /**
     * Saves a current active record.
     *
     * @return object
     */
    public function save()
    {
        $this->where('id', '=', $this->attributes['id'])->update($this->attributes);
        return $this->find($this->attributes['id']);
    }
    /**
     * Soft deletes a record.
     *
     * @return int
     */
    public function delete()
    {
        $this->where('id', '=', $this->attributes['id']);
        return parent::delete();
    }
    /**
     * Restores a record.
     *
     * @return int
     */
    public function restore()
    {
        $this->where('id', '=', $this->attributes['id']);
        return parent::restore();
    }
    /**
     * Deletes a record.
     *
     * @return int
     */
    public function forceDelete()
    {
        $this->where('id', '=', $this->attributes['id']);
        return parent::forceDelete();
    }
    /**
     * Finds a record by primary key.
     *
     * @param  int $id
     * @return object
     */
    public function find(int $id)
    {
        $res = parent::find($id);
        if ($res != null) {
            $this->hasResults = true;
            foreach ($res as $key => $value) {
                $this->attributes[$key] = $value;
            }
            $this->loadRelations();
        } else {
            $this->hasResults = false;
        }
        return $this;
    }
    /**
     * Executes a count(*) query and returns count.
     *
     * @param  mixed $fields
     * @return int
     */
    public function count($fields = false)
    {
        return parent::count($fields);
    }
    /**
     * Executes a max(*) query and returns max.
     *
     * @param  mixed $fields
     * @return int
     */
    public function max($fields = false)
    {
        return parent::max($fields);
    }
    /**
     * Executes a min(*) query and returns min.
     *
     * @param  mixed $fields
     * @return int
     */
    public function min($fields = false)
    {
        return parent::min($fields);
    }
    /**
     * Executes an avg(*) query and returns avg.
     *
     * @param  mixed $fields
     * @return int
     */
    public function avg($fields = false)
    {
        return parent::avg($fields);
    }
    /**
     * Executes a sum(*) query and returns sum.
     *
     * @param  mixed $fields
     * @return int
     */
    public function sum($fields = false)
    {
        return parent::sum($fields);
    }
    /**
     * Retrieves first record.
     *
     * @return object
     */
    public function first()
    {
        $res = parent::first();
        if ($res != null) {
            $this->hasResults = true;
            foreach ($res as $key => $value) {
                $this->attributes[$key] = $value;
            }
            $this->loadRelations();    
        }else {
            $this->hasResults = false;
        }
        return $this;
    }
    /**
     * Indicates if the query returned results.
     *
     * @return bool
     */
    public function hasResults()
    {
        return $this->hasResults;
    }
    /**
     * Retrieves all records.
     *
     * @return \Zefire\Database\Collection
     */
    public function get()
    {
        return new Collection($this->model, parent::get());
    }
    /**
     * Paginates a record set.
     *
     * @param  int $perPage
     * @return \Zefire\Database\Collection
     */
    public function paginate(int $perPage)
    {
        $select = $this->select;
        $count = $this->count();
        $this->select = $select;
        $inputs = (\Request::method() =='GET') ? \Request::inputs() : $inputs = [];
        $current = (isset($inputs['page']) && $inputs['page'] != '') ? $inputs['page'] : 1;
        $pages = $count / $perPage;
        $this->limit = $perPage;
        switch ($current) {
            case 1:
                break;
            case 2:
                $this->offset = $perPage;
                break;
            default:
                $this->offset = ($current * $perPage) - ($perPage);
                break;
        }
        $collection = new Collection($this->model, parent::get());
        $collection->setPagination($count, $pages);
        return $collection;
    }
    /**
     * Defines which relations should be pulled with records.
     *
     * @param  mixed $relations
     * @return object
     */
    public function with($relations)
    {
        $relations = $this->stringToArray($relations);
        foreach ($relations as $relation) {
            $this->with[] = $relation;          
        }
        return $this;       
    }
    /**
     * Performs a one to one relationship.
     *
     * @param  string $related
     * @param  string $foreignKey
     * @return mixed
     */
    public function hasOne(string $related, string $foreignKey)
    {
        $related_instance = $this->newInstance($related);
        return $related_instance->where($related_instance->primaryKey, '=', $this->attributes[$foreignKey])->first();
    }
    /**
     * Performs a one to many relationship.
     *
     * @param  string $related
     * @param  string $foreignKey
     * @return mixed
     */
    public function hasMany(string $related, string $foreignKey)
    {
        $related_instance = $this->newInstance($related);
        return $related_instance->where($related_instance->primaryKey, '=', $this->attributes[$foreignKey])->get();
    }
    /**
     * Performs a many to many relationship.
     *
     * @param  string $related
     * @param  string $pivot
     * @param  string $ownerKey
     * @param  string $foreignKey
     * @return mixed
     */
    public function manyToMany(string $related, string $pivot, string $ownerKey, string $foreignKey)
    {
        $pivot_instance = $this->newInstance($pivot);
        $this->pivot = $pivot_instance->where($ownerKey, '=', $this->attributes['id'])->get();
        if ($this->pivot->count > 0) {
            $ids = [];
            foreach ($this->pivot->items as $pivot_record) {
                $ids[] = $pivot_record->$foreignKey;
            }
            $related_instance = $this->newInstance($related);
            $related_instance = $related_instance->in('id', $ids)->get();

            $related_instance->parentId = $this->id;
            $related_instance->ownerKey = $ownerKey;
            $related_instance->foreignKey = $foreignKey;
            $related_instance->pivot = $pivot;
            $related_instance->related = $this->newInstance($related);
            return $related_instance;
        } else {
            $this->pivot = null;
        }       
    }
    /**
     * Gets related records for a relation.
     *
     * @return object
     */
    public function loadRelations()
    {
        $relations = [];
        foreach ($this->with as $with) {
            $this->$with = $this->$with();
        }
        return $this;
    }
    /**
     * Converts record to an array of attributes.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->attributes;
    }
    /**
     * Creates a new model instance.
     *
     * @return object
     */
    protected function newInstance($model)
    {
        return new $model();
    }    
}
