<?php

namespace Zefire\FileSystem;

use Zefire\Contracts\Fillable;

class FileSystem implements Fillable
{
    /**
     * Container holding all disks.
     *
     * @var array
     */
    protected $store = [];
    /**
     * Holds current disk.
     *
     * @var mixed
     */
    protected $disk;
    /**
     * Stores current file name.
     *
     * @var string
     */
    protected $name;
    /**
     * Stores current file content.
     *
     * @var string
     */
    protected $data;
    /**
     * Creates a new file system instance.
     *
     * @return void
     */
    public function __construct()
    {
        $config = \App::config('file');
        foreach ($config['disks'] as $key => $disk) {
            try {
                $this->store[$key] = \App::make($disk['driver']);
                $this->store[$key]->mount($disk['config']);
            } catch (\Exception $e) {
                $this->store[$key] = \App::make('Zefire\FileSystem\FileAdapter');
                $this->store[$key]->mount(['path' => '/tmp']);
            }
        }        
        $this->disk($config['default']);
    }
    /**
     * Defines the current disk to use.
     *
     * @param  string $disk
     * @return \Zefire\FileSystem\FileSystem
     */
    public function disk($disk)
    {
        if (isset($this->store[$disk])) {
            $this->disk = $this->store[$disk];
            return $this;    
        } else {
            throw new \Exception('Disk "' . $disk . '" is not available');
        }        
    }
    /**
     * Creates a file if it does not exists
     * and puts the content in the file.
     *
     * @param  string $file
     * @param  string $content
     * @return string
     */
    public function put($file, $content)
    {
        $this->disk->put($file, $content);
        return $this->disk->get($file);
    }
    /**
     * Prepends content to an existing file.
     *
     * @param  string $file
     * @param  string $content
     * @return string
     */
    public function prepend($file, $content)
    {
        if ($this->exists($file)) {
            return $this->put($file, $content . $this->disk->get($file));
        }        
    }
    /**
     * Appends content to an existing file.
     *
     * @param  string $file
     * @param  string $content
     * @return string
     */
    public function append($file, $content)
    {
        if ($this->exists($file)) {
            return $this->put($file, $this->disk->get($file) . $content);
        }        
    }
    /**
     * returns path.
     *
     * @return string
     */
    public function path()
    {
        return $this->disk->path();
    }
    /**
     * Retrieves a file content.
     *
     * @param  string $file
     * @return string
     */
    public function get($file)
    {
        $this->name = $file;
        $this->data = $this->disk->get($file);
        return $this->data;
    }
    /**
     * Saves the current active file on current disk.
     *
     * @param  string $content
     * @return string
     */
    public function save($content)
    {
        $this->disk->put($this->name, $content);
        return $this->disk->get($this->name);
    }
    /**
     * Lists files from directory.
     *
     * @param  string $directory
     * @return array
     */
    public function list($directory = '')
    {
        return $this->disk->list($directory);
    }
    /**
     * Checks if a file exists.
     *
     * @param  string $file
     * @return bool
     */
    public function exists($file)
    {
        return $this->disk->exists($file);
    }
    /**
     * Deletes a file.
     *
     * @param  string $file
     * @return bool
     */
    public function delete($file)
    {
        return $this->disk->delete($file);
    }
    /**
     * Gets a file's size.
     *
     * @param  string $file
     * @return string
     */
    public function size($file)
    {
        return $this->disk->size($file);
    }
    /**
     * Gets a file's lat modified datetime.
     *
     * @param  string $file
     * @return string
     */
    public function lastModified($file)
    {
        return $this->disk->lastModified($file);
    }
    /**
     * Gets a file's type.
     *
     * @param  string $file
     * @return string
     */
    public function type($file)
    {
        return $this->disk->type($file);
    }
}