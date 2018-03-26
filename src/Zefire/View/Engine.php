<?php

namespace Zefire\View;

use Zefire\Contracts\ViewEngine;
use Zefire\FileSystem\FileSystem;

class Engine implements ViewEngine
{
	/**
     * Stores a FileSystem instance.
     *
     * @var \Zefire\FileSystem\FileSystem
     */
	protected $fileSystem;
	/**
     * Compiled view file extension.
     *
     * @var string
     */
	protected $extension = '.php';
	/**
     * Stores a file instance.
     *
     * @param  \Zefire\FileSystem\FileSystem $fileSystem
     * @return void
     */
	public function __construct(FileSystem $fileSystem)
	{
		$this->fileSystem = $fileSystem;
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
		$this->fileSystem->disk('compiled')->put($filename . $this->extension, $data);
	}
	/**
     * Checks if a compiled view exists.
     *
     * @param  string $filename
     * @return bool
     */
	public function exists($filename)
	{
		return ($this->fileSystem->disk('compiled')->exists($filename . $this->extension, $data)) ? true : false;
	}
	/**
     * Gets a compiled view.
     *
     * @param  string $filename
     * @return string
     */
	public function get($filename)
	{
		if ($this->fileSystem->disk('compiled')->exists($filename . $this->extension)) {
			return $this->fileSystem->disk('compiled')->get($filename . $this->extension);
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
		if ($this->fileSystem->disk('compiled')->exists($filename . $this->extension)) {
			if ((time() - $this->fileSystem->disk('compiled')->lastModified($filename . $this->extension)) > \App::config('view.max_life')) {
				$this->fileSystem->disk('compiled')->delete($filename . $this->extension);
				return true;
			}
			return false;
		} else {
			return true;
		}
	}
}