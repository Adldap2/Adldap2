<?php

namespace Adldap\Query;

use Adldap\Query\Bindings\Where;

class Grammar
{
    /**
     * Wraps a query string in brackets.
     *
     * Produces: (query)
     *
     * @param string $query
     * @param string $prefix
     * @param string $suffix
     *
     * @return string
     */
    public function wrap($query, $prefix = '(', $suffix = ')')
    {
        return $prefix.$query.$suffix;
    }

    /**
     * Compiles the Builder instance into an LDAP query string.
     *
     * @param \Adldap\Query\Builder $builder
     *
     * @return string
     */
    public function compileQuery(Builder $builder)
    {
        // Retrieve the query 'where' bindings.
        $wheres = $builder->getWheres();

        // Retrieve the query 'orWhere' bindings.
        $orWheres = $builder->getOrWheres();

        // Retrieve the query filter bindings.
        $filters = $builder->getFilters();

        // We'll combine all raw filters together first.
        $query = implode(null, $filters);

        // Compile wheres.
        $query = $this->compileWheres($wheres, $query);

        // Compile or wheres.
        $query = $this->compileOrWheres($orWheres, $query);

        // Count the total amount of filters.
        $total = count($wheres) + count($filters);

        // Make sure we wrap the query in an 'and' if using
        // multiple filters. We also need to check if only
        // one where is used with multiple orWheres, that
        // we wrap it in an `and` query.
        if ($total > 1 || (count($wheres) === 1 && count($orWheres) > 0)) {
            $query = $this->compileAnd($query);
        }

        return $query;
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
    public function compileEquals($field, $value)
    {
        return $this->wrap($field.Operator::$equals.$value);
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
    public function compileDoesNotEqual($field, $value)
    {
        return $this->wrap(Operator::$doesNotEqual.$this->compileEquals($field, $value));
    }

    /**
     * Returns a query string for greater than or equals.
     *
     * Produces: (field>=value)
     *
     * @param string $field
     * @param string $value
     *
     * @return string
     */
    public function compileGreaterThanOrEquals($field, $value)
    {
        return $this->wrap($field.Operator::$greaterThanOrEquals.$value);
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
    public function compileLessThanOrEquals($field, $value)
    {
        return $this->wrap($field.Operator::$lessThanOrEquals.$value);
    }

    /**
     * Returns a query string for approximately equals.
     *
     * Produces: (field~=value)
     *
     * @param string $field
     * @param string $value
     *
     * @return string
     */
    public function compileApproximatelyEquals($field, $value)
    {
        return $this->wrap($field.Operator::$approximatelyEquals.$value);
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
    public function compileStartsWith($field, $value)
    {
        return $this->wrap($field.Operator::$equals.$value.Operator::$has);
    }

    /**
     * Returns a query string for does not start with.
     *
     * Produces: (!(field=*value))
     *
     * @param string $field
     * @param string $value
     *
     * @return string
     */
    public function compileNotStartsWith($field, $value)
    {
        return $this->wrap(Operator::$doesNotEqual.$this->compileStartsWith($field, $value));
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
    public function compileEndsWith($field, $value)
    {
        return $this->wrap($field.Operator::$equals.Operator::$has.$value);
    }

    /**
     * Returns a query string for does not end with.
     *
     * Produces: (!(field=value*))
     *
     * @param string $field
     * @param string $value
     *
     * @return string
     */
    public function compileNotEndsWith($field, $value)
    {
        return $this->wrap(Operator::$doesNotEqual.$this->compileEndsWith($field, $value));
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
    public function compileContains($field, $value)
    {
        return $this->wrap($field.Operator::$equals.Operator::$has.$value.Operator::$has);
    }

    /**
     * Returns a query string for does not contain.
     *
     * Produces: (!(field=*value*))
     *
     * @param string $field
     * @param string $value
     *
     * @return string
     */
    public function compileNotContains($field, $value)
    {
        return $this->wrap(Operator::$doesNotEqual.$this->compileContains($field, $value));
    }

    /**
     * Returns a query string for a where has.
     *
     * Produces: (field=*)
     *
     * @param string $field
     *
     * @return string
     */
    public function compileHas($field)
    {
        return $this->wrap($field.Operator::$equals.Operator::$has);
    }

    /**
     * Returns a query string for a where does not have.
     *
     * Produces: (!(field=*))
     *
     * @param string $field
     *
     * @return string
     */
    public function compileNotHas($field)
    {
        return $this->wrap(Operator::$doesNotEqual.$this->compileHas($field));
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
    public function compileAnd($query)
    {
        return $this->wrap($query, '(&');
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
    public function compileOr($query)
    {
        return $this->wrap($query, '(|');
    }

    /**
     * Assembles all where clauses in the current wheres property.
     *
     * @param array  $wheres
     * @param string $query
     *
     * @return string
     */
    protected function compileWheres(array $wheres, $query = '')
    {
        foreach ($wheres as $where) {
            $query .= $this->compileWhere($where);
        }

        return $query;
    }

    /**
     * Assembles all or where clauses in the current orWheres property.
     *
     * @param array  $orWheres
     * @param string $query
     *
     * @return string
     */
    protected function compileOrWheres(array $orWheres, $query = '')
    {
        $ors = '';

        foreach ($orWheres as $where) {
            $ors .= $this->compileWhere($where);
        }

        // Make sure we wrap the query in an 'or' if using multiple
        // orWheres. For example (|(QUERY)(ORWHEREQUERY)).
        if (($query && count($orWheres) > 0) || count($orWheres) > 1) {
            $query .= $this->compileOr($ors);
        } else {
            $query .= $ors;
        }

        return $query;
    }

    /**
     * Assembles a single where query based
     * on its operator and returns it.
     *
     * @param Where $where
     *
     * @return string|null
     */
    protected function compileWhere(Where $where)
    {
        // The compile function prefix.
        $prefix = 'compile';

        // Get the operator from the where.
        $operator = $where->getOperator();

        // Get the name of the operator.
        $name = array_search($operator, Operator::all());

        if ($name !== false) {
            // If the name was found we'll camel case it
            // to run it through the compile method.
            $method = $prefix.ucfirst($name);

            // Make sure the compile method exists for the operator.
            if (method_exists($this, $method)) {
                return $this->{$method}($where->getField(), $where->getValue());
            }
        }
    }
}
