<?php

namespace Zefire\Database;

class Collection
{
	/**
     * Stores collected items
     *
     * @var array
     */
	public $items = [];
	/**
     * Stores a count of collected items.
     * 
     * @var int
     */
    public $count;
    /**
     * Stores a relation's parent ID.
     * 
     * @var int
     */
    public $parentId = null;
    /**
     * Stores a relation's owner key.
     * 
     * @var string
     */
    public $ownerKey = null;
    /**
     * Stores a relation's foreign key.
     * 
     * @var string
     */
    public $foreignKey = null;
    /**
     * Stores relation's pivot name.
     * 
     * @var string
     */
    public $pivot = null;
    /**
     * Stores relation's instance.
     * 
     * @var object
     */
    public $related = null;
    /**
     *Creates a new collection instance.
     *
     * @param  string $model
     * @param  array  $records
     * @return void
     */
	public function __construct($model, $records = [])
	{
		foreach ($records as $key => $value) {
			$record = new $model();
			foreach ($value as $k => $v) {
				$record->$k = $v;
			}
			$record->loadRelations();
			$this->items[] = $record; 
		}
		$this->count = count($this->items);
	}
	/**
     * Creates pivot records to create relations between entities.
     *
     * @param  array  $ids
     * @return object
     */
    public function attach(array $ids)
    {
        $pivot = $this->newInstance($this->pivot);
        foreach ($ids as $id) {
        	$pivot->create([$this->ownerKey => $this->parentId, $this->foreignKey => $id]);
        }
    }
    /**
     * Deletes pivot records to remove relations between entities.
     *
     * @param  array  $ids
     * @return object
     */
    public function detach(array $ids)
    {
        $pivot = $this->newInstance($this->pivot);
        $pivot_records = $pivot->where($this->ownerKey, '=', $this->parentId)->whereIn($this->foreignKey, $ids)->get();
        foreach ($pivot_records->items as $record) {
    		$record->delete();        		
    	}        
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