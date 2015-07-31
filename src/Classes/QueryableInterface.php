<?php

namespace Adldap\Classes;

interface QueryableInterface
{
    /**
     * Finds a record from AD.
     *
     * @param string $name
     * @param array  $fields
     *
     * @return bool|\Adldap\Models\Entry
     */
    public function find($name, $fields = []);

    /**
     * Get all of the records from AD.
     *
     * @param array     $fields
     * @param bool|true $sorted
     * @param string    $sortBy
     * @param string    $sortByDirection
     *
     * @return array|bool
     */
    public function all($fields = [], $sorted = true, $sortBy = 'cn', $sortByDirection = 'asc');

    /**
     * Creates a new Search limited to the current classes object type.
     *
     * @return Search
     */
    public function search();
}
