<?php

namespace Adldap\Classes;

use Adldap\Adldap;
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
     * Stores the default fields to query.
     *
     * @var array
     */
    protected $fields = array(
        'cn',
        'description',
        'displayname',
        'distinguishedname',
        'samaccountname',
        "objectcategory",
        "operatingsystem",
        "operatingsystemservicepack",
        "operatingsystemversion"
    );

    /**
     * Stores the selects to use in the query when assembled.
     *
     * @var array
     */
    protected $selects = array();

    /**
     * Stores the wheres to use in the query when assembled.
     *
     * @var array
     */
    protected $wheres = array();

    /**
     * The opening query string.
     *
     * @var string
     */
    protected static $openQuery = '(';

    /**
     * The closing query string.
     *
     * @var string
     */
    protected static $closeQuery = ')';

    /**
     * Performs the current query on the current LDAP connection.
     *
     * @return array|bool
     */
    public function get()
    {
        $this->assembleQuery();

        $results = $this->connection->search($this->adldap->getBaseDn(), $this->getQuery(), $this->getSelects());

        if($results) return $this->processResults($results);

        return false;
    }

    /**
     * Adds the inserted fields to query on the current LDAP connection.
     *
     * @param array $fields
     * @return $this
     */
    public function select($fields = array())
    {
        if(is_array($fields))
        {
            foreach($fields as $field)
            {
                $this->addSelect($field);
            }
        } else if(is_string($fields))
        {
            $this->addSelect($fields);
        }

        return $this;
    }

    /**
     * @param $field
     * @param null $operator
     * @param null $value
     * @return $this
     */
    public function where($field, $operator = null, $value = null)
    {
        $this->addWhere($field, $operator, $value);

        return $this;
    }

    /**
     * Returns true / false depending if the current object
     * contains selects.
     *
     * @return bool
     */
    public function hasSelects()
    {
        if(count($this->selects) > 0) return true;

        return false;
    }

    /**
     * Returns the current selected fields to retrieve.
     *
     * @return array
     */
    public function getSelects()
    {
        $fields = $this->fields;

        /*
         * If the current search object has specific
         * search fields, we'll use those instead.
         */
        if($this->hasSelects()) $fields = $this->selects;

        return $fields;
    }

    /**
     *
     *
     * @param $field
     * @param string $direction
     */
    public function sortBy($field, $direction = 'desc')
    {

    }

    /**
     * Returns the current LDAP query string.
     *
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Adds the inserted field to the selects property.
     *
     * @param string $field
     */
    private function addSelect($field)
    {
        $this->selects[] = $field;
    }

    /**
     * Adds the inserted field, operator and value to the wheres
     * property array.
     *
     * @param string $field
     * @param string $operator
     * @param null $value
     * @throws AdldapException
     */
    private function addWhere($field, $operator, $value = null)
    {
        $this->wheres[] = array(
            'field' => $field,
            'operator' => $this->getOperator($operator),
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
        if(count($this->wheres) > 0)
        {
            foreach($this->wheres as $where)
            {
                switch($where['operator'])
                {
                    case '=':
                        $this->query .= $this->queryEquals($where['field'], $where['value']);
                        break;
                    case '!':
                        $this->query .= $this->queryDoesNotEqual($where['field'], $where['value']);
                        break;
                    case '*':
                        $this->query .= $this->queryWildcard($where['field']);
                        break;
                }
            }

            if(count($this->wheres) > 1)
            {
                $this->query = $this->queryAnd($this->query);
            }
        }
    }

    /**
     * Returns a query string for does not equal.
     *
     * @param string $field
     * @param string $value
     * @return string
     */
    private function queryDoesNotEqual($field, $value)
    {
        return $this::$openQuery . '!' . $this->queryEquals($field, $value) . $this::$closeQuery;
    }

    /**
     * Returns a query string for equals.
     *
     * @param string $field
     * @param string $value
     * @return string
     */
    private function queryEquals($field, $value)
    {
        return $this::$openQuery . $field . '=' . $value . $this::$closeQuery;
    }

    /**
     * Returns a query string for a wildcard.
     *
     * @param string $field
     * @return string
     */
    private function queryWildcard($field)
    {
        return $this::$openQuery .  $field . '=*' . $this::$closeQuery;
    }

    /**
     * Wraps the inserted query inside an AND operator.
     *
     * @param $query
     * @return string
     */
    private function queryAnd($query)
    {
        return $this::$openQuery . '&' . $query . $this::$closeQuery;
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

        if(array_key_exists($key, $this->operators)) return $this->operators[$key];

        $operators = implode(', ', $this->operators);

        $message = "Operator: $operator cannot be used in an LDAP query. Available operators are $operators";

        throw new AdldapException($message);
    }

    /**
     * Processes LDAP search results into a nice array.
     *
     * @param resource $results
     * @return array
     */
    private function processResults($results)
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
}