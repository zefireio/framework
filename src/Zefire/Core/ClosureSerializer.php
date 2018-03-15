<?php

namespace Zefire\Core;

use Zefire\Core\Serializable;

class ClosureSerializer
{
	use Serializable;
	/**
     * Stores a closure.
     *
     * @var mixed
     */
	protected $closure = null;
	/**
     * Stores a reflector.
     *
     * @var \ReflectionFunction
     */
	protected $reflection = null;
	/**
     * Stores a closure's code.
     *
     * @var mixed
     */
	protected $code = null;
	/**
     * Stores a closure's variables.
     *
     * @var array
     */
	protected $used_variables = [];
	/**
     * Creates a new closure manager instance.
     *
     * @return void
     */
	public function __construct($closure)
	{
		if (!$closure instanceOf \Closure) {
			throw new \Exception();
		}
		$this->closure 			= $closure;
		$this->reflection 		= new \ReflectionFunction($closure);
		$this->code 			= $this->fetchCode();
		$this->used_variables 	= $this->fetchUsedVariables();
	}
	/**
     * Returns the reflector as a function.
     *
     * @return \ReflectionFunction
     */
	public function __invoke()
	{
		$args = func_get_args();
		return $this->reflection->invokeArgs($args);
	}
	/**
     * Gets the closure.
     *
     * @return \Closure
     */
	public function getClosure()
	{
		return $this->closure;
	}
	/**
     * Extracts the closure's code.
     *
     * @return string
     */
	protected function fetchCode()
	{
		$file = new \SplFileObject($this->reflection->getFileName());
		$file->seek($this->reflection->getStartLine() - 1);
		$code = '';
		while ($file->key() < $this->reflection->getEndLine()) {
			$code .= $file->current();
			$file->next();
		}
		$begin = strpos($code, 'function');
		$end = strrpos($code, '}');
		$code = substr($code, $begin, $end - $begin + 1);
		return $code;
	}
	/**
     * Gets the closure's code.
     *
     * @return string
     */
	public function getCode()
	{
		return $this->code;
	}
	/**
     * Gets the closure's parameters.
     *
     * @return array
     */
	public function getParameters()
	{
		return $this->reflection->getParameters();
	}
	/**
     * Extracts the closure's variables.
     *
     * @return array
     */
	protected function fetchUsedVariables()
	{
		$use_index = stripos($this->code, 'use');
		if (!$use_index) {
			return [];
		}
		$begin = strpos($this->code, '(', $use_index) + 1;
		$end = strpos($this->code, ')', $begin);
		$vars = explode(',', substr($this->code, $begin, $end - $begin));
		$static_vars = $this->reflection->getStaticVariables();
		$used_vars = array();
		foreach ($vars as $var) {
			$var = trim($var, ' $&amp;');
			$used_vars[$var] = $static_vars[$var];
		}
		return $used_vars;
	}
	/**
     * Gets the closure's variables.
     *
     * @return array
     */
	public function getUsedVariables()
	{
		return $this->used_variables;
	}
	/**
     * Saves the closures code and variables on serialization.
     *
     * @return array
     */
	public function __sleep()
	{
		return array('code', 'used_variables');
	}
	/**
     * Restores the closures code and variables on serialization.
     *
     * @return void
     */
	public function __wakeup()
	{
		extract($this->used_variables);
		eval('$_function = ' . $this->code . ';');
		if (isset($_function) && $_function instanceOf \Closure) {
			$this->closure = $_function;
			$this->reflection = new \ReflectionFunction($_function);
		} else{
			throw new \Exception();
		}
	}
}