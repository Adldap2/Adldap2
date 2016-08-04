<?php

namespace Adldap\Models\Traits;

trait ModelTrait
{
    /**
     * Returns the current query builder.
     *
     * @return \Adldap\Query\Builder
     */
    abstract public function getQuery();

    /**
     * Returns the current models schema.
     *
     * @return \Adldap\Contracts\Schemas\SchemaInterface
     */
    abstract public function getSchema();

    /**
     * Returns the model's distinguished name string.
     *
     * (Alias for getDistinguishedName())
     *
     * https://msdn.microsoft.com/en-us/library/aa366101(v=vs.85).aspx
     *
     * @return string|null
     */
    abstract public function getDn();

    /**
     * Returns the models attribute with the specified key.
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
     * @return \Adldap\Models\Model
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
