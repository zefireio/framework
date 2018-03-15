<?php

namespace Zefire\Contracts;

interface Fillable
{
    /**
     * returns path.
     *
     * @return string
     */
    public function path();
    /**
     * Lists files from directory.
     *
     * @param  string $directory
     * @return array
     */
    public function list($directory = '');
    /**
     * Checks if a file exists.
     *
     * @param  string $file
     * @return bool
     */
    public function exists($file);
    /**
     * Retrieves a file content.
     *
     * @param  string $file
     * @return string
     */
    public function get($file);
    /**
     * Creates a file if it does not exists
     * and puts the content in the file.
     *
     * @param  string $file
     * @param  string $contents
     * @return string
     */
    public function put($file, $contents);
    /**
     * Deletes a file.
     *
     * @param  string $file
     * @return bool
     */
    public function delete($file);
    /**
     * Gets a file's size.
     *
     * @param  string $file
     * @return string
     */
    public function size($file);
    /**
     * Gets a file's lat modified datetime.
     *
     * @param  string $file
     * @return string
     */
    public function lastModified($file);
    /**
     * Gets a file's type.
     *
     * @param  string $file
     * @return string
     */
    public function type($file);
}