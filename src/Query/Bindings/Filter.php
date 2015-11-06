<?php

namespace Adldap\Query\Bindings;

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
