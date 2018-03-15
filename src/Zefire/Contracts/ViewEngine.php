<?php

namespace Zefire\Contracts;

interface ViewEngine
{
    /**
     * Stores a compiled template output in a file.
     *
     * @param  string $filename
     * @param  string $data
     * @return void
     */
    public function put($filename, $data);
    /**
     * Checks if a compiled view exists.
     *
     * @param  string $filename
     * @return bool
     */
    public function exists($filename);
    /**
     * Gets a compiled view.
     *
     * @param  string $filename
     * @return string
     */
    public function get($filename);
    /**
     * Checks if a compiled view as expired.
     *
     * @param  string $filename
     * @return int
     */
    public function expired($filename);
}
