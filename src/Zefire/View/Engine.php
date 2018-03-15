<?php

namespace Zefire\View;

use Zefire\Contracts\ViewEngine;
use Zefire\FileSystem\File;

class Engine implements ViewEngine
{
	/**
     * Stores a file instance.
     *
     * @var \Zefire\FileSystem\File
     */
	protected $file;
	/**
     * Compiled view file extension.
     *
     * @var string
     */
	protected $extension = '.php';
	/**
     * Stores a file instance.
     *
     * @param  \Zefire\FileSystem\File $file
     * @return void
     */
	public function __construct(File $file)
	{
		$this->file = $file;
	}
	/**
     * Stores a compiled template output in a file.
     *
     * @param  string $filename
     * @param  string $data
     * @return void
     */
	public function put($filename, $data)
	{
		$this->file->put(\App::compiledPath() . $filename . $this->extension, $data);		
	}
	/**
     * Checks if a compiled view exists.
     *
     * @param  string $filename
     * @return bool
     */
	public function exists($filename)
	{
		return ($this->file->exists(\App::compiledPath() . $filename . $this->extension)) ? 1 : 0;
	}
	/**
     * Gets a compiled view.
     *
     * @param  string $filename
     * @return string
     */
	public function get($filename)
	{
		if ($this->file->exists(\App::compiledPath() . $filename . $this->extension)) {
			return $this->file->get(\App::compiledPath() . $filename . $this->extension);
		} else {
			throw new \Exception('File does not exist');
		}
	}
	/**
     * Checks if a compiled view as expired.
     *
     * @param  string $filename
     * @return int
     */
	public function expired($filename)
	{
		if ($this->file->exists(\App::compiledPath() . $filename . $this->extension)) {
			if (filemtime(\App::compiledPath() . $filename . $this->extension) + \App::config('view.max_life') < time() && file_exists(\App::compiledPath() . $filename . $this->extension)) {
	            unlink(\App::compiledPath() . $filename . $this->extension);
	            return 1;
	        } else {
	        	return 0;
	        }
	    } else {
	    	return 1;
	    }
	}
}