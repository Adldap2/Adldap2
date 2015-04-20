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
        '>=', // Greater than or equal to
        '<=', // Less than or equal to
        '&' // And
    );

    /**
     * Stores the default fields to query.
     *
     * @var array
     */
    protected $fields = array(
        'anr',
        'cn',
        'mail',
        'description',
        'displayname',
        'distinguishedname',
        'samaccountname',
        "objectcategory",
        "objectclass",
        "operatingsystem",
        "operatingsystemservicepack",
        "operatingsystemversion",
        "msExchUserAccountControl",
        "msExchMasterAccountSID",
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
     * Stores the orWheres to use in the query
     * when assembled.
     *
     * @var array
     */
    protected $orWheres = array();

    /**
     * Stores the field to sort search results by.
     *
     * @var string
     */
    protected $sortByField = '';

    /**
     * Stores the direction to sort the search results by.
     *
     * @var string
     */
    protected $sortByDirection = 'DESC';

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
     * Performs the specified query on the current LDAP connection.
     *
     * @param string $query
     * @return mixed
     */
    public function query($query)
    {
        $results = $this->connection->search($this->adldap->getBaseDn(), $query, $this->getSelects());

        if ($results) return $this->processResults($results);

        return false;
    }

    /**
     * Performs a global 'all' search query on the
     * current connection.
     *
     * @return array|bool
     */
    public function all()
    {
        $this->where('objectClass', '*');

        return $this->get();
    }

    /**
     * Performs the current query on the current LDAP connection.
     *
     * @return array|bool
     */
    public function get()
    {
        return $this->query($this->getQuery(), $this->getSelects());
    }

    /**
     * Adds the inserted fields to query on the current LDAP connection.
     *
     * @param array $fields
     * @return $this
     */
    public function select($fields = array())
    {
        if (is_array($fields))
        {
            foreach($fields as $field)
            {
                $this->addSelect($field);
            }
        } else if (is_string($fields))
        {
            $this->addSelect($fields);
        }

        return $this;
    }

    /**
     * Adds a where clause to the current query.
     *
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
     * Adds an orWhere clause to the current query.
     *
     * @param string $field
     * @param null $operator
     * @param null $value
     * @return $this
     */
    public function orWhere($field, $operator = null, $value = null)
    {
        $this->addOrWhere($field, $operator, $value);

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
        if (count($this->selects) > 0) return true;

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
        if ($this->hasSelects()) $fields = $this->selects;

        return $fields;
    }

    /**
     * Returns the wheres on the current search object.
     *
     * @return array
     */
    public function getWheres()
    {
        return $this->wheres;
    }

    /**
     * Returns the or wheres on the current search object.
     *
     * @return array
     */
    public function getOrWheres()
    {
        return $this->orWheres;
    }

    /**
     * Returns the current LDAP query string.
     *
     * @return string
     */
    public function getQuery()
    {
        // Return the query if it exists
        if ( ! empty($this->query)) return $this->query;

        /*
         * Looks like our query hasn't been assembled
         * yet, let's try to assemble it
         */
        $this->assembleQuery();

        // Return the assembled query
        return $this->query;
    }

    /**
     * Sorts the LDAP search results by the specified field
     * and direction.
     *
     * @param $field
     * @param string $direction
     * @return $this
     */
    public function sortBy($field, $direction = 'desc')
    {
        $this->sortByField = $field;

        if(strtolower($direction) === 'asc')
        {
            $this->sortByDirection = SORT_ASC;
        } else
        {
            $this->sortByDirection = SORT_DESC;
        }

        return $this;
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
     * Adds the inserted field, operator and value
     * to the wheres property array.
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
     * Adds the inserted field, operator and value
     * to the orWheres property array.
     *
     * @param string $field
     * @param string $operator
     * @param null $value
     * @throws AdldapException
     */
    private function addOrWhere($field, $operator, $value = null)
    {
        $this->orWheres[] = array(
            'field' => $field,
            'operator' => $this->getOperator($operator),
            'value' => $this->connection->escape($value),
        );
    }

    /**
     * Sets the query property.
     *
     * @param string $query
     */
    private function setQuery($query)
    {
        $this->query = $query;
    }

    /**
     * Adds the specified query onto the current query.
     *
     * @param string $query
     */
    private function addToQuery($query)
    {
        $this->query .= $query;
    }

    /**
     * Returns an assembled query using the current object parameters.
     *
     * @return string
     */
    private function assembleQuery()
    {
        $this->assembleWheres();

        $this->assembleOrWheres();

        /*
         * Make sure we wrap the query in an 'and'
         * if using multiple wheres or if we have any
         * orWheres. For example (&(cn=John*)(|(description=User*)))
         */
        if (count($this->getWheres()) > 1 || count($this->getOrWheres()) > 0)
        {
            $this->setQuery($this->queryAnd($this->getQuery()));
        }
    }

    /**
     * Assembles all where clauses in the current wheres property.
     *
     * @return void
     */
    private function assembleWheres()
    {
        if (count($this->wheres) > 0)
        {
            foreach($this->wheres as $where)
            {
                switch($where['operator'])
                {
                    case '=':
                        $this->addToQuery($this->queryEquals($where['field'], $where['value']));
                        break;
                    case '!':
                        $this->addToQuery($this->queryDoesNotEqual($where['field'], $where['value']));
                        break;
                    case '*':
                        $this->addToQuery($this->queryWildcard($where['field']));
                        break;
                }
            }
        }
    }

    /**
     * Assembles all or where clauses in the current orWheres property.
     *
     * @return void
     */
    private function assembleOrWheres()
    {
        if (count($this->orWheres) > 0)
        {
            $ors = '';

            foreach($this->orWheres as $where)
            {
                switch($where['operator'])
                {
                    case '=':
                        $ors .= $this->queryEquals($where['field'], $where['value']);
                        break;
                    case '!':
                        $ors .= $this->queryDoesNotEqual($where['field'], $where['value']);
                        break;
                    case '*':
                        $ors .= $this->queryWildcard($where['field']);
                        break;
                }
            }

            /*
             * Make sure we wrap the query in an 'and'
             * if using multiple wheres. For example (&QUERY)
             */
            if (count($this->orWheres) > 0) $this->addToQuery($this->queryOr($ors));
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
     * @param string $query
     * @return string
     */
    private function queryAnd($query)
    {
        return $this::$openQuery . '&' . $query . $this::$closeQuery;
    }

    /**
     * Wraps the inserted query inside an OR operator.
     *
     * @param string $query
     * @return string
     */
    private function queryOr($query)
    {
        return $this::$openQuery . '|' . $query . $this::$closeQuery;
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

        if (array_key_exists($key, $this->operators)) return $this->operators[$key];

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

        if ( ! empty($this->sortByField)) return $this->processSortBy($objects);

        return $objects;
    }

    /**
     * Processes the array of specified object results
     * and sorts them by the field and direction search
     * property.
     *
     * @param $objects
     * @param array
     */
    private function processSortBy($objects)
    {
        if(count($objects) > 0)
        {
            foreach($objects as $key => $row)
            {
                if(array_key_exists($this->sortByField, $row))
                {
                    $sort[$key] = $row[$this->sortByField];
                }
            }

            array_multisort($sort, $this->sortByDirection, $objects);
        }

        return $objects;
    }
}