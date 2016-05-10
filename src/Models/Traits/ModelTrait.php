<?php

namespace Adldap\Models\Traits;

trait ModelTrait
{
    /**
     * {@inheritdoc}
     */
    abstract public function getQuery();

    /**
     * {@inheritdoc}
     */
    abstract public function getSchema();

    /**
     * {@inheritdoc}
     */
    abstract public function getDn();

    /**
     * {@inheritdoc}
     */
    abstract public function getAttribute($key, $subKey = null);

    /**
     * {@inheritdoc}
     */
    abstract public function setAttribute($key, $value, $subKey = null);

    /**
     * {@inheritdoc}
     */
    abstract public function convertStringToBool($bool);
}
