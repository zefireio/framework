<?php

namespace Zefire\Validation;

class Validator
{
	/**
     * Stores a list of validation rules.
     *
     * @var array
     */
	protected $rules = [
		'required', 'unique', 'min', 'max', 'numeric', 'integer', 'email'
	];
	/**
     * Stores a list of rules who passed validation.
     *
     * @var array
     */
	protected $passed = [];
	/**
     * Stores a list of rules who failed validation.
     *
     * @var array
     */
	protected $failed = [];
	/**
     * Validates a given input against a given set of rules.
     *
     * @param  array $rules
     * @param  array $data
     * @return void
     */
	public function validate($rules, $data)
	{
		foreach ($rules as $key => $value) {
			$expressions = explode('|', $value);
			foreach ($expressions as $k => $v) {
				if (strstr($v, ':')) {
					$exp = explode(':', $v);
					if (in_array($exp[0], $this->rules)) {
						$method = $exp[0];
						$status = $this->$method($key, $data[$key], $exp[1]);
						if ($status) {
							$this->passed[$exp[0]][$key] = $data[$key];
						} else {
							$this->failed[$exp[0]][$key] = $data[$key];
						}
					} else {
						throw new \Exception('Rule "' . $exp[0] . '" is not supported');
					}
				} else {
					if (in_array($v, $this->rules)) {
						$status = $this->$v($key, $data[$key]);
						if ($status) {
							$this->passed[$v][$key] = $data[$key];
						} else {
							$this->failed[$v][$key] = $data[$key];
						}
					} else {
						throw new \Exception('Rule "' . $v . '" is not supported');
					}
				}
			}
		}
	}
	/**
     * Checks if the dataset passed validation.
     *
     * @return bool
     */
	public function passes()
	{
		return (empty($this->failed)) ? true : false;
	}
	/**
     * Checks if the dataset failed validation.
     *
     * @return bool
     */
	public function fails()
	{
		return (!empty($this->failed)) ? true : false;
	}
	/**
     * Returns validation failure messages.
     *
     * @return array
     */
	public function messages()
	{
		return $this->failed;
	}
	/**
     * Checks if an input is empty.
     *
     * @param  string $field
     * @param  string $value
     * @return bool
     */
	protected function required($field, $value)
	{
		return ($field != '') ? true : false;
	}
	/**
     * Checks if an input is unique against a database table.
     *
     * @param  string $field
     * @param  string $value
     * @param  string $model
     * @return bool
     */
	protected function unique($field, $value, $model)
	{
		$model = '\\App\\Models\\' . $model;
		$model = new $model();
		return ($model->where($field, '=', $value)->count() == 0) ? true : false;
	}
	/**
     * Checks if an input has at least the minimum required length.
     *
     * @param  string $field
     * @param  string $value
     * @param  string $length
     * @return bool
     */
	protected function min($field, $value, $length)
	{
		return (strlen($value) > $length) ? true : false;
	}
	/**
     * Checks if an input is below or equal the maximum accepted length.
     *
     * @param  string $field
     * @param  string $value
     * @param  string $length
     * @return bool
     */
	protected function max($field, $value, $length)
	{
		return (strlen($value) <= $length) ? true : false;
	}
	/**
     * Checks if an input is numeric.
     *
     * @param  string $field
     * @param  string $value
     * @return bool
     */
	protected function numeric($field, $value)
	{
		return (is_numeric($value)) ? true : false;
	}
	/**
     * Checks if an input is an integer.
     *
     * @param  mixed $field
     * @param  string $value
     * @return bool
     */
	protected function integer($field, $value)
	{
		return (is_int($value)) ? true : false;
	}
	/**
     * Checks if an input is a valid email address.
     *
     * @param  string $field
     * @param  string $value
     * @return bool
     */
	protected function email($field, $value)
	{
		return (filter_var($value, FILTER_VALIDATE_EMAIL)) ? true : false;
	}
}