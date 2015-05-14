<?php

namespace Adldap\Query;

use Adldap\Exceptions\InvalidQueryOperator;
use Adldap\Interfaces\ConnectionInterface;

/**
 * Class Builder.
 */
class Builder
{
    /**
     * Stores the current connection.
     *
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * Stores the current query string.
     *
     * @var string
     */
    protected $query = '';

    /**
     * Stores the selects to use in the query when assembled.
     *
     * @var array
     */
    protected $selects = [];

    /**
     * Stores the current where filters
     * on the current query.
     *
     * @var array
     */
    protected $wheres = [];

    /**
     * Stores the current or where filters
     * on the current query.
     *
     * @var array
     */
    protected $orWheres = [];

    /**
     * The opening query string.
     *
     * @var string
     */
    protected static $open = '(';

    /**
     * The closing query string.
     *
     * @var string
     */
    protected static $close = ')';

    /**
     * Constructor.
     *
     * @param ConnectionInterface $connection
     */
    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Returns the current query.
     *
     * @return string
     */
    public function get()
    {
        // Return the query if it exists
        if (!empty($this->query)) {
            return $this->query;
        }

        /*
         * Looks like our query hasn't been assembled
         * yet, let's try to assemble it
         */
        $this->assembleQuery();

        // Return the assembled query
        return $this->query;
    }

    /**
     * Adds the inserted fields to query on the current LDAP connection.
     *
     * @param array $fields
     *
     * @return $this
     */
    public function select($fields = [])
    {
        if (is_array($fields)) {
            foreach ($fields as $field) {
                $this->addSelect($field);
            }
        } elseif (is_string($fields)) {
            $this->addSelect($fields);
        }

        return $this;
    }

    /**
     * Adds a where clause to the current query.
     *
     * @param string $field
     * @param string $operator
     * @param string $value
     *
     * @return $this
     */
    public function where($field, $operator = null, $value = null)
    {
        $this->addWhere($field, $operator, $value);

        return $this;
    }

    /**
     * Adds an or where clause to the current query.
     *
     * @param string $field
     * @param string $operator
     * @param string $value
     *
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
        if (count($this->selects) > 0) {
            return true;
        }

        return false;
    }

    /**
     * Returns the current selected fields to retrieve.
     *
     * @return array
     */
    public function getSelects()
    {
        return $this->selects;
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
     * Returns the current query string.
     *
     * @return string
     */
    public function getQuery()
    {
        // Return the query if it exists
        if (!empty($this->query)) {
            return $this->query;
        }

        /*
         * Looks like our query hasn't been assembled
         * yet, let's try to assemble it
         */
        $this->assembleQuery();

        // Return the assembled query
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
     * Adds the inserted field, operator and value
     * to the wheres property array.
     *
     * @param string $field
     * @param string $operator
     * @param null   $value
     *
     * @throws InvalidQueryOperator
     */
    private function addWhere($field, $operator, $value = null)
    {
        $this->wheres[] = [
            'field' => $field,
            'operator' => $this->getOperator($operator),
            'value' => $this->connection->escape($value),
        ];
    }

    /**
     * Adds the inserted field, operator and value
     * to the orWheres property array.
     *
     * @param string $field
     * @param string $operator
     * @param null   $value
     *
     * @throws InvalidQueryOperator
     */
    private function addOrWhere($field, $operator, $value = null)
    {
        $this->orWheres[] = [
            'field' => $field,
            'operator' => $this->getOperator($operator),
            'value' => $this->connection->escape($value),
        ];
    }

    /**
     * Returns a query string for does not equal.
     *
     * Produces: (!(field=value))
     *
     * @param string $field
     * @param string $value
     *
     * @return string
     */
    private function buildDoesNotEqual($field, $value)
    {
        return $this::$open.Operator::$doesNotEqual.$this->buildEquals($field, $value).$this::$close;
    }

    /**
     * Returns a query string for equals.
     *
     * Produces: (field=value)
     *
     * @param string $field
     * @param string $value
     *
     * @return string
     */
    private function buildEquals($field, $value)
    {
        return $this::$open.$field.Operator::$equals.$value.$this::$close;
    }

    /**
     * Returns a query string for greater than or equals.
     *
     * Produces: (field<=value)
     *
     * @param string $field
     * @param string $value
     *
     * @return string
     */
    private function buildGreaterThanOrEquals($field, $value)
    {
        return $this::$open.$field.Operator::$greaterThanOrEqual.$value.$this::$close;
    }

    /**
     * Returns a query string for less than or equals.
     *
     * Produces: (field<=value)
     *
     * @param string $field
     * @param string $value
     *
     * @return string
     */
    private function buildLessThanOrEquals($field, $value)
    {
        return $this::$open.$field.Operator::$lessThanOrEqual.$value.$this::$close;
    }

    /**
     * Returns a query string for approximately equals.
     *
     * Produces: (field=value)
     *
     * @param string $field
     * @param string $value
     *
     * @return string
     */
    private function buildApproximatelyEquals($field, $value)
    {
        return $this::$open.$field.Operator::$approximateEqual.$value.$this::$close;
    }

    /**
     * Returns a query string for starts with.
     *
     * Produces: (field=value*)
     *
     * @param string $field
     * @param string $value
     *
     * @return string
     */
    private function buildStartsWith($field, $value)
    {
        return $this::$open.$field.Operator::$equals.$value.Operator::$wildcard.$this::$close;
    }

    /**
     * Returns a query string for ends with.
     *
     * Produces: (field=*value)
     *
     * @param string $field
     * @param string $value
     *
     * @return string
     */
    private function buildEndsWith($field, $value)
    {
        return $this::$open.$field.Operator::$equals.Operator::$wildcard.$value.$this::$close;
    }

    /**
     * Returns a query string for contains.
     *
     * Produces: (field=*value*)
     *
     * @param string $field
     * @param string $value
     *
     * @return string
     */
    private function buildContains($field, $value)
    {
        return $this::$open.$field.Operator::$equals.Operator::$wildcard.$value.Operator::$wildcard.$this::$close;
    }

    /**
     * Returns a query string for a wildcard.
     *
     * Produces: (field=*)
     *
     * @param string $field
     *
     * @return string
     */
    private function buildWildcard($field)
    {
        return $this::$open.$field.Operator::$equals.Operator::$wildcard.$this::$close;
    }

    /**
     * Wraps the inserted query inside an AND operator.
     *
     * Produces: (&query)
     *
     * @param string $query
     *
     * @return string
     */
    private function buildAnd($query)
    {
        return $this::$open.Operator::$and.$query.$this::$close;
    }

    /**
     * Wraps the inserted query inside an OR operator.
     *
     * Produces: (|query)
     *
     * @param string $query
     *
     * @return string
     */
    private function buildOr($query)
    {
        return $this::$open.Operator::$or.$query.$this::$close;
    }

    /**
     * Retrieves an operator from the available operators.
     *
     * Throws an AdldapException if no operator is found.
     *
     * @param $operator
     *
     * @return string
     *
     * @throws InvalidQueryOperator
     */
    private function getOperator($operator)
    {
        $operators = $this->getOperators();

        $key = array_search(strtolower($operator), $operators);

        if ($key !== false && array_key_exists($key, $operators)) {
            return $operators[$key];
        }

        $operators = implode(', ', $operators);

        $message = "Operator: $operator cannot be used in an LDAP query. Available operators are $operators";

        throw new InvalidQueryOperator($message);
    }

    /**
     * Returns an array of available operators.
     *
     * @return array
     */
    private function getOperators()
    {
        return [
            Operator::$wildcard,
            Operator::$equals,
            Operator::$doesNotEqual,
            Operator::$greaterThanOrEqual,
            Operator::$lessThanOrEqual,
            Operator::$approximateEqual,
            Operator::$startsWith,
            Operator::$endsWith,
            Operator::$contains,
            Operator::$and,
        ];
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
        if (count($this->getWheres()) > 1 || count($this->getOrWheres()) > 0) {
            $this->setQuery($this->buildAnd($this->getQuery()));
        }
    }

    /**
     * Assembles all where clauses in the current wheres property.
     */
    private function assembleWheres()
    {
        if (count($this->wheres) > 0) {
            foreach ($this->wheres as $where) {
                $this->addToQuery($this->assembleWhere($where));
            }
        }
    }

    /**
     * Assembles all or where clauses in the current orWheres property.
     */
    private function assembleOrWheres()
    {
        if (count($this->orWheres) > 0) {
            $ors = '';

            foreach ($this->orWheres as $where) {
                $ors .= $this->assembleWhere($where);
            }

            /*
             * Make sure we wrap the query in an 'and'
             * if using multiple wheres. For example (&QUERY)
             */
            if (count($this->orWheres) > 0) {
                $this->addToQuery($this->buildOr($ors));
            }
        }
    }

    /**
     * Assembles a single where query based
     * on its operator and returns it.
     *
     * @param array $where
     *
     * @return string|null
     */
    private function assembleWhere($where = array())
    {
        if (is_array($where)) {
            switch ($where['operator']) {
                case Operator::$equals:
                    return $this->buildEquals($where['field'], $where['value']);
                case Operator::$doesNotEqual:
                    return $this->buildDoesNotEqual($where['field'], $where['value']);
                case Operator::$greaterThanOrEqual:
                    return $this->buildGreaterThanOrEquals($where['field'], $where['value']);
                case Operator::$lessThanOrEqual:
                    return $this->buildLessThanOrEquals($where['field'], $where['value']);
                case Operator::$approximateEqual:
                    return $this->buildApproximatelyEquals($where['field'], $where['value']);
                case Operator::$startsWith:
                    return $this->buildStartsWith($where['field'], $where['value']);
                case Operator::$endsWith:
                    return $this->buildEndsWith($where['field'], $where['value']);
                case Operator::$contains:
                    return $this->buildContains($where['field'], $where['value']);
                case Operator::$wildcard:
                    return $this->buildWildcard($where['field']);
            }
        }

        return;
    }

    /**
     * Adds the specified query onto the current query.
     *
     * @param string $query
     */
    private function addToQuery($query)
    {
        $this->query .= (string) $query;
    }

    /**
     * Sets the current query property.
     *
     * @param string $query
     */
    private function setQuery($query)
    {
        $this->query = (string) $query;
    }
}
