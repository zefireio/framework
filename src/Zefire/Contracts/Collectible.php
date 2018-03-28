<?php

namespace Zefire\Contracts;

interface Collectible
{
    /**
     * Build a pagination index.
     *
     * @param  int  $total
     * @param  int  $pages
     * @return void
     */
    public function setPagination($total, $pages);
    /**
     * Creates pivot records to create relations between entities.
     *
     * @param  array  $ids
     * @return object
     */
    public function attach(array $ids);
    /**
     * Synchronises pivot records for relations between entities.
     *
     * @param  array  $ids
     * @return object
     */
    public function sync(array $ids);
    /**
     * Deletes pivot records to remove relations between entities.
     *
     * @param  array  $ids
     * @return object
     */
    public function detach(array $ids);
}