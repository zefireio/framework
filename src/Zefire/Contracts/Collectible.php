<?php

namespace Zefire\Contracts;

interface Collectible
{
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