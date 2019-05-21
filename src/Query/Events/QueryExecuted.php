<?php

namespace Adldap\Query\Events;

class QueryExecuted
{
    /**
     * The base distinguished name that the query was executed upon.
     *
     * @var string
     */
    protected $base;

    /**
     * The LDAP filter that was used for the query.
     *
     * @var string
     */
    protected $query;

    /**
     * The attributes that were selected for the query.
     *
     * @var array
     */
    protected $selects = [];

    /**
     * The number of milliseconds it took to execute the query.
     *
     * @var float
     */
    protected $time;

    /**
     * Constructor.
     *
     * @param string     $base
     * @param string     $query
     * @param array      $selects
     * @param null|float $time
     */
    public function __construct($base, $query, $selects = [], $time = null)
    {
        $this->base = $base;
        $this->query = $query;
        $this->selects = $selects;
        $this->time = $time;
    }

    /**
     * Returns the base DN.
     *
     * @return string
     */
    public function getBase()
    {
        return $this->base;
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function getSelects()
    {
        return $this->selects;
    }

    public function getTime()
    {
        return $this->time;
    }
}
