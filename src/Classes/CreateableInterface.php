<?php

namespace Adldap\Classes;

interface CreateableInterface
{
    /**
     * Creates and returns a new model instance.
     *
     * @param array $attributes
     *
     * @return \Adldap\Models\Entry
     */
    public function newInstance(array $attributes = []);

    /**
     * Creates and saves a new model instance, then returns the result.
     *
     * @param array $attributes
     *
     * @return bool
     */
    public function create(array $attributes = []);
}
