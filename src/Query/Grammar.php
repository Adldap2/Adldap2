<?php

namespace Adldap\Query;

class Grammar
{
    /**
     * Wraps a query string in brackets.
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
        $query = implode(null, $builder->filters);

        $query = $this->compileWheres($builder, $query);

        $query = $this->compileOrWheres($builder, $query);

        // Count the total amount of filters.
        $total = count($builder->wheres)
            + count($builder->filters);

        // Make sure we wrap the query in an 'and' if using
        // multiple filters. We also need to check if only
        // one where is used with multiple orWheres, that
        // we wrap it in an 'and' query.
        if ($total > 1 || (count($builder->wheres) === 1 && count($builder->orWheres) > 0)) {
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
     * @param Builder $builder
     * @param string  $query
     *
     * @return string
     */
    protected function compileWheres(Builder $builder, $query = '')
    {
        foreach ($builder->wheres as $where) {
            $query .= $this->compileWhere($where);
        }

        return $query;
    }

    /**
     * Assembles all or where clauses in the current orWheres property.
     *
     * @param Builder $builder
     * @param string  $query
     *
     * @return string
     */
    protected function compileOrWheres(Builder $builder, $query = '')
    {
        $ors = '';

        foreach ($builder->orWheres as $where) {
            $ors .= $this->compileWhere($where);
        }

        // Make sure we wrap the query in an 'or' if using
        // multiple orWheres. For example (|(QUERY)(ORWHEREQUERY)).
        if ((!empty($query) && count($builder->getOrWheres()) > 0) || count($builder->getOrWheres()) > 1) {
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
     * @param array $where
     *
     * @return string|null
     */
    protected function compileWhere(array $where = [])
    {
        // The compile function prefix.
        $prefix = 'compile';

        // Make sure the operator key exists inside the where clause.
        if (array_key_exists(Builder::$whereOperatorKey, $where)) {
            // Get the operator from the where.
            $operator = $where[Builder::$whereOperatorKey];

            // Get the name of the operator.
            $name = array_search($operator, Operator::all());

            if ($name !== false) {
                // If the name was found we'll camel case it
                // to run it through the compile method.
                $method = $prefix.ucfirst($name);

                // Make sure the compile method exists for the operator.
                if (method_exists($this, $method)) {
                    return $this->{$method}($where[Builder::$whereFieldKey], $where[Builder::$whereValueKey]);
                }
            }
        }
    }
}
