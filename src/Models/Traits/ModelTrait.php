<?php

namespace Adldap\Models\Traits;

trait ModelTrait
{
    /**
     * Returns the current query builder instance.
     *
     * @var \Adldap\Query\Builder
     */
    abstract public function getQuery();

    /**
     * Returns the current models schema.
     *
     * @var \Adldap\Contracts\Schemas\SchemaInterface
     */
    abstract public function getSchema();

    /**
     * Retrieves the specified key from the attribute array.
     *
     * If a sub-key is specified, it will try and
     * retrieve it from the parent keys array.
     *
     * @param int|string $key
     * @param int|string $subKey
     *
     * @return mixed
     */
    abstract public function getAttribute($key, $subKey = null);

    /**
     * Sets attributes on the current entry.
     *
     * @param int|string $key
     * @param mixed      $value
     * @param int|string $subKey
     *
     * @return $this
     */
    abstract public function setAttribute($key, $value, $subKey = null);

    /**
     * Converts the inserted string boolean to a PHP boolean.
     *
     * @param string $bool
     *
     * @return null|bool
     */
    abstract public function convertStringToBool($bool);
}
