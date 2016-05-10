<?php

namespace Adldap\Query\Bindings;

use InvalidArgumentException;

class Filter extends AbstractBinding
{
    /**
     * The raw filter query.
     *
     * @var string
     */
    protected $query;

    /**
     * Constructor.
     *
     * @param string $query
     */
    public function __construct($query)
    {
        if (!is_string($query)) {
            throw new InvalidArgumentException('Query filter must be a string.');
        }

        $this->query = $query;
    }

    /**
     * Returns the filter query.
     *
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Returns the filter query.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getQuery();
    }
}
