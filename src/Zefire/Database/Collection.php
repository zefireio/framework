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
     * Exports a collection's items to an array.
     *
     * @return array
     */
	public function toArray()
	{
		return $this->items;
	}
}