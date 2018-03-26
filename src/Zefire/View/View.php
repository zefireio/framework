<?php

namespace Zefire\View;

use Zefire\FileSystem\FileSystem;
use Zefire\View\Compiler;
use Zefire\View\Engine;
use Zefire\Hashing\Hasher;

class View
{
	/**
     * Stores the view FileSystem instance.
     *
     * @var \Zefire\FileSystem\FileSystem
     */
     protected $fileSystem;
     /**
     * Stores the view compiler instance.
     *
     * @var \Zefire\View\Compiler
     */
	protected $compiler;
	/**
     * Stores the view engine instance.
     *
     * @var \Zefire\View\Engine
     */
	protected $engine;
	/**
     * Stores the hasher instance.
     *
     * @var \Zefire\Hashing\Hasher
     */
	protected $hasher;
	/**
     * Stores the template's content.
     *
     * @var string
     */
	protected $template;
	/**
     * Stores files extension.
     *
     * @var string
     */
	protected $fileExtension = '.php';
	/**
     * Stores the view's data.
     *
     * @var mixed
     */
	protected $data;
	/**
     * Stores the compiled view's content.
     *
     * @var string
     */
	protected $compiled;
	/**
     * Creates a new view instance.
     *
     * @param  \Zefire\FileSystem\FileSystem  $fileSystem
     * @param  \Zefire\View\Engine            $engine
     * @param  \Zefire\View\Compiler          $compiler
     * @param  \Zefire\Hashing\Hasher         $hasher
     * @return void
     */
	public function __construct(FileSystem $fileSystem, Engine $engine, Compiler $compiler, Hasher $hasher)
	{
		$this->fileSystem = $fileSystem;
          $this->engine     = $engine;
		$this->compiler   = $compiler;
		$this->hasher     = $hasher;
	}
	/**
     * Renders a compiled view to the browser.
     *
     * @param  string $template
     * @param  mixed  $data
     * @return string
     */
	public function render($template, $data = false)
	{
		$this->data = $data;
          $this->getTemplate($template);
		$this->generateId();
		if (\App::config('view.force_compile') === true) {
			$this->compiled = $this->compiler->make($this->template, $data);
			$this->engine->put($this->id, $this->compiled);
		} else {
			if ($this->engine->expired($this->id)) {
				$this->compiled = $this->compiler->make($this->template, $data);
				$this->engine->put($this->id, $this->compiled);
			}	
		}		
		$obLevel = ob_get_level();
		ob_start();
		try {
            include \App::compiledPath() . DIRECTORY_SEPARATOR . $this->id . $this->fileExtension;
        } catch (Exception $exception) {
            throw new \Exception($exception, $obLevel);            
        }
        return ltrim(ob_get_clean());
	}
	/**
     * Gets the template.
     *
     * @param  string $template
     * @return void
     */
	protected function getTemplate($template)
	{
		$this->template = $this->fileSystem->disk('templates')->get($this->toPath($template) . $this->fileExtension);          
	}
	/**
     * Validates a file path.
     *
     * @param  string $filename
     * @return string
     */
	protected function toPath($filename)
	{
		return str_replace("'", "", str_replace('.', '/', $filename));
	}	
	/**
     * Generates the compiled view's id used as file name.
     *
     * @return string
     */
	protected function generateId()
	{
		$this->id = $this->hasher->make($this->template, 'sha1');
		return $this->id;
	}
}