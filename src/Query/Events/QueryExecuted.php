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
     * Returns the base distinguished name that the query was executed upon.
     *
     * @return string
     */
    public function getBase()
    {
        return $this->base;
    }

    /**
     * Returns the LDAP filter that was used for the query.
     *
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Returns the attributes that were selected for the query.
     *
     * @return array
     */
    public function getSelects()
    {
        return $this->selects;
    }

    /**
     * Returns the number of milliseconds it took to execute the query.
     *
     * @return float|null
     */
    public function getTime()
    {
        return $this->time;
    }
}
