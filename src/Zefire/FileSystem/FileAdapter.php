<?php

namespace Zefire\FileSystem;

use Zefire\Contracts\Fillable;

class FileAdapter implements Fillable
{
    /**
     * Stores disk path.
     *
     * @var string
     */
    protected $path;
    /**
     * Mounts a disk.
     *
     * @param  array $config
     * @return void
     */
    public function mount(array $config)
    {
        if (isset($config['path']) && $config['path'] != '') {
            $this->path = $config['path'];
        } else {
            throw new \Exception('Please define the "path" for your local storage.');
        }        
    }
    /**
     * returns path.
     *
     * @return string
     */
    public function path()
    {
        return $this->path;
    }
    /**
     * Lists files from directory.
     *
     * @param  string $directory
     * @return array
     */
    public function list($directory = '')
    {
        return scandir($this->path . $directory);
    }
    /**
     * Checks if a file exists.
     *
     * @param  string $file
     * @return bool
     */
    public function exists($file)
    {
        return file_exists($this->path . $file);
    }
    /**
     * Retrieves a file content.
     *
     * @param  string $file
     * @return string
     */
    public function get($file)
    {
        if ($this->exists($file)) {
            return file_get_contents($this->path . $file);
        } else {
            throw new \Exception("File does not exist at path " . $this->path . $file);    
        }        
    }
    /**
     * Creates a file if it does not exists
     * and puts the content in the file.
     *
     * @param  string $file
     * @param  string $contents
     * @param  int    $lock
     * @return string
     */
    public function put($file, $contents, $lock = false)
    {
        return file_put_contents($this->path . $file, $contents, $lock ? LOCK_EX : 0);
    }
    /**
     * Deletes a file.
     *
     * @param  string $file
     * @return bool
     */
    public function delete($file)
    {
        return (!@unlink($this->path . $file)) ? true : false;
    }
    /**
     * Gets a file's size.
     *
     * @param  string $file
     * @return string
     */
    public function size($file)
    {
        return filesize($this->path . $file);
    }
    /**
     * Gets a file's lat modified datetime.
     *
     * @param  string $file
     * @return string
     */
    public function lastModified($file)
    {
        return filemtime($this->path . $file);
    }
    /**
     * Gets a file's type.
     *
     * @param  string $file
     * @return string
     */
    public function type($file)
    {
        return finfo_file(finfo_open(FILEINFO_MIME_TYPE), $this->path . $file);
    }
}