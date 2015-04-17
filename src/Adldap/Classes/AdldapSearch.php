<?php

namespace Adldap\Classes;

use Adldap\Exceptions\AdldapException;
use Adldap\Objects\LdapEntry;

/**
 * Class AdldapSearch
 * @package Adldap\Classes
 */
class AdldapSearch extends AdldapBase
{
    /**
     * Stores the current query string.
     *
     * @var string
     */
    protected $query = '';

    /**
     * Stores available operators to use for a query.
     *
     * @var array
     */
    protected $operators = array(
        '*', // Wildcard, All
        '!', // Does not equal
        '=', // Does equal
        '&' // And
    );

    /**
     * Stores the default fields to query
     *
     * @var array
     */
    protected $fields = array(
        'cn',
        'description',
        'displayname',
        'distinguishedname',
        'samaccountname',
    );

    /**
     * Stores the wheres to use in the query when assembled.
     *
     * @var array
     */
    protected $wheres = array();

    protected $andWheres = array();

    protected static $openQuery = '(';

    protected static $closeQuery = ')';

    /**
     * Performs the current query on the current LDAP connection.
     *
     * @return array|bool
     */
    public function get()
    {
        $this->query = $this->assembleQuery();

        $results = $this->connection->search($this->adldap->getBaseDn(), $this->query, $this->fields);

        if($results)
        {
            $entries = $this->connection->getEntries($results);

            $objects = array();

            for ($i = 0; $i < $entries["count"]; $i++)
            {
                $entry = new LdapEntry($entries[$i], $this->connection);

                $objects[] = $entry->getAttributes();
            }

            return $objects;
        }

        return false;
    }

    public function select()
    {

    }

    public function where($field, $operator = null, $value = null)
    {
        $operator = $this->getOperator($operator);

        $this->addWhere($field, $operator, $value);

        return $this;
    }

    private function addSelect()
    {

    }

    private function addWhere($field, $operator, $value)
    {
        $this->wheres[] = array(
            'field' => $field,
            'operator' => $operator,
            'value' => $this->connection->escape($value),
        );
    }

    /**
     * Returns an assembled query using the current object parameters.
     *
     * @return string
     */
    private function assembleQuery()
    {
        $query = '';

        foreach($this->wheres as $where)
        {
            $query .= $this::$openQuery
                . $where['field']
                . $where['operator']
                . $where['value']
                . $this::$closeQuery;
        }

        return $query;
    }

    /**
     * Retrieves an operator from the available operators.
     *
     * Throws an AdldapException if no operator is found.
     *
     * @param $operator
     * @return string
     * @throws AdldapException
     */
    private function getOperator($operator)
    {
        $key = array_search($operator, $this->operators);

        if($key) return $this->operators[$key];

        $operators = implode(',', $this->operators);

        $message = "Operator: $operator cannot be used in an LDAP query. Available operators are $operators";

        throw new AdldapException($message);
    }
}