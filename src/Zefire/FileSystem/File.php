<?php

namespace Zefire\FileSystem;

class File
{
    /**
     * Scans a folder to list it's content.
     *
     * @param  string $path
     * @return array
     */
    public function files($path)
    {
        return scandir($path);
    }
    /**
     * Checks if a file exists.
     *
     * @param  string $path
     * @return bool
     */
    public function exists($path)
    {
        return file_exists($path);
    }
    /**
     * Gets the content of a file.
     *
     * @param  string $path
     * @return string
     */
    public function get($path)
    {
        if ($this->isFile($path)) {
            return file_get_contents($path);
        } else {
            throw new \Exception("File does not exist at path {$path}");    
        }        
    }
    /**
     * Hashes a file's content.
     *
     * @param  string $path
     * @return string
     */
    public function hash($path)
    {
        return md5_file($path);
    }
    /**
     * Puts content in a file.
     *
     * @param  string $path
     * @param  string $content
     * @param  int    $lock
     * @return mixed
     */
    public function put($path, $content, $lock = false)
    {
        return file_put_contents($path, $content, $lock ? LOCK_EX : 0);
    }
    /**
     * Add content to the begining of a file.
     *
     * @param  string $path
     * @param  string $data
     * @return mixed
     */
    public function prepend($path, $data)
    {
        if ($this->exists($path)) {
            return $this->put($path, $data . $this->get($path));
        }
        return $this->put($path, $data);
    }
    /**
     * Appends content to an existing file.
     *
     * @param  string $path
     * @param  string $data
     * @return mixed
     */
    public function append($path, $data)
    {
        return file_put_contents($path, $data, FILE_APPEND);
    }
    /**
     * Changes a file's permissions.
     *
     * @param  string $path
     * @param  mixed  $mode
     * @return mixed
     */
    public function chmod($path, $mode = null)
    {
        if ($mode) {
            return chmod($path, $mode);
        }
        return substr(sprintf('%o', fileperms($path)), -4);
    }
    /**
     * Deletes files.
     *
     * @param  mixed $paths
     * @return bool
     */
    public function delete($paths)
    {
        $paths = is_array($paths) ? $paths : func_get_args();
        $success = true;
        foreach ($paths as $path) {
            try {
                if (!@unlink($path)) {
                    $success = false;
                }
            } catch (ErrorException $e) {
                $success = false;
            }
        }
        return $success;
    }
    /**
     * Move a file from one location to another.
     *
     * @param  string $path
     * @param  string  $target
     * @return mixed
     */
    public function move($path, $target)
    {
        return rename($path, $target);
    }
    /**
     * Copies a file.
     *
     * @param  string $path
     * @param  mixed  $target
     * @return mixed
     */
    public function copy($path, $target)
    {
        return copy($path, $target);
    }
    /**
     * Gets a file's name.
     *
     * @param  string $path
     * @return string
     */
    public function name($path)
    {
        return pathinfo($path, PATHINFO_FILENAME);
    }
    /**
     * Gets a file's base path.
     *
     * @param  string $path
     * @return string
     */
    public function basename($path)
    {
        return pathinfo($path, PATHINFO_BASENAME);
    }
    /**
     * Gets a file's directory.
     *
     * @param  string $path
     * @return string
     */
    public function dirname($path)
    {
        return pathinfo($path, PATHINFO_DIRNAME);
    }
    /**
     * Gets a file's extension.
     *
     * @param  string $path
     * @return string
     */
    public function extension($path)
    {
        return pathinfo($path, PATHINFO_EXTENSION);
    }
    /**
     * Gets a file's type.
     *
     * @param  string $path
     * @return string
     */
    public function type($path)
    {
        return filetype($path);
    }
    /**
     * Gets a file's MIME Type.
     *
     * @param  string $path
     * @return string
     */
    public function mimeType($path)
    {
        return finfo_file(finfo_open(FILEINFO_MIME_TYPE), $path);
    }
    /**
     * Gets a file's size.
     *
     * @param  string $path
     * @return float
     */
    public function size($path)
    {
        return filesize($path);
    }
    /**
     * Gets a file's last modified datetime.
     *
     * @param  string $path
     * @return string
     */
    public function lastModified($path)
    {
        return filemtime($path);
    }
    /**
     * Checks if a path is a directory.
     *
     * @param  string $directory
     * @return string
     */
    public function isDirectory($directory)
    {
        return is_dir($directory);
    }
    /**
     * Checks if a file is a readable.
     *
     * @param  string $path
     * @return bool
     */
    public function isReadable($path)
    {
        return is_readable($path);
    }
    /**
     * Checks if a file is a writable.
     *
     * @param  string $path
     * @return bool
     */
    public function isWritable($path)
    {
        return is_writable($path);
    }
    /**
     * Checks if a file is a file or a directory.
     *
     * @param  string $file
     * @return string
     */
    public function isFile($file)
    {
        return is_file($file);
    }
}