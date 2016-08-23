<?php

namespace Adldap\Query\Bindings;

use Adldap\Utilities;
use Adldap\Query\Operator;
use Adldap\Exceptions\InvalidQueryOperatorException;

class Where extends AbstractBinding
{
    /**
     * The field of the where binding.
     *
     * @var string
     */
    protected $field;

    /**
     * The operator of the where binding.
     *
     * @var string
     */
    protected $operator;

    /**
     * The value of the where binding.
     *
     * @var string
     */
    protected $value;

    /**
     * Constructor.
     *
     * @param string $field
     * @param string $operator
     * @param string $value
     *
     * @throws InvalidQueryOperatorException
     */
    public function __construct($field, $operator, $value)
    {
        // We'll escape the field to avoid allowing unsafe characters inside.
        $this->field = Utilities::escape($field, null, 3);

        // Validate and retrieve the operator.
        $this->operator = $this->validateOperator($operator);

        // Completely escape the value.
        $this->value = Utilities::escape($value);
    }

    /**
     * Returns the where bindings field.
     *
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Returns the where bindings operator.
     *
     * @return string
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * Returns the where bindings value.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Retrieves an operator from the available operators.
     *
     * Throws an AdldapException if no operator is found.
     *
     * @param string $operator
     *
     * @throws InvalidQueryOperatorException
     *
     * @return string
     */
    protected function validateOperator($operator)
    {
        $operators = Operator::all();

        $key = array_search(strtolower($operator), $operators);

        if ($key !== false && array_key_exists($key, $operators)) {
            return $operators[$key];
        }

        $operators = implode(', ', $operators);

        $message = "Operator: $operator cannot be used in an LDAP query. Available operators are: $operators";

        throw new InvalidQueryOperatorException($message);
    }
}
