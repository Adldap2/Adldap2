<?php

namespace Adldap\Query;

use Adldap\Classes\Utilities;
use Adldap\Exceptions\InvalidQueryOperatorException;
use Adldap\Schemas\ActiveDirectory;

class Builder
{
    /**
     * The field key for a where statement.
     *
     * @var string
     */
    public static $whereFieldKey = 'field';

    /**
     * The operator key for a where statement.
     *
     * @var string
     */
    public static $whereOperatorKey = 'operator';

    /**
     * The value key for a where statement.
     *
     * @var string
     */
    public static $whereValueKey = 'value';

    /**
     * Stores the column selects to use in the query when assembled.
     *
     * @var array
     */
    public $selects = [];

    /**
     * Stores the current where filters
     * on the current query.
     *
     * @var array
     */
    public $wheres = [];

    /**
     * Stores the current or where filters
     * on the current query.
     *
     * @var array
     */
    public $orWheres = [];

    /**
     * Stores the current grammar instance.
     *
     * @var Grammar
     */
    protected $grammar;

    /**
     * Constructor.
     *
     * @param Grammar $grammar
     */
    public function __construct(Grammar $grammar)
    {
        $this->grammar = $grammar;
    }

    /**
     * Returns the current query.
     *
     * @return string
     */
    public function get()
    {
        return $this->grammar->compileQuery($this);
    }

    /**
     * Returns the current Grammar instance.
     *
     * @return Grammar
     */
    public function getGrammar()
    {
        return $this->grammar;
    }

    /**
     * Adds the inserted fields to query on the current LDAP connection.
     *
     * @param array $fields
     *
     * @return Builder
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
     * @param string      $field
     * @param string|null $operator
     * @param string|null $value
     *
     * @return Builder
     */
    public function where($field, $operator = null, $value = null)
    {
        $this->addWhere($field, $operator, $value);

        return $this;
    }

    /**
     * Adds a where contains clause to the current query.
     *
     * @param string $field
     * @param string $value
     *
     * @return Builder
     */
    public function whereContains($field, $value)
    {
        $this->addWhere($field, Operator::$contains, $value);

        return $this;
    }

    /**
     * Adds a where starts with clause to the current query.
     *
     * @param string $field
     * @param string $value
     *
     * @return Builder
     */
    public function whereStartsWith($field, $value)
    {
        $this->addWhere($field, Operator::$startsWith, $value);

        return $this;
    }

    /**
     * Adds a where ends with clause to the current query.
     *
     * @param string $field
     * @param string $value
     *
     * @return Builder
     */
    public function whereEndsWith($field, $value)
    {
        $this->addWhere($field, Operator::$endsWith, $value);

        return $this;
    }

    /**
     * Adds an or where clause to the current query.
     *
     * @param string      $field
     * @param string|null $operator
     * @param string|null $value
     *
     * @return Builder
     */
    public function orWhere($field, $operator = null, $value = null)
    {
        $this->addOrWhere($field, $operator, $value);

        return $this;
    }

    /**
     * Adds an or where contains clause to the current query.
     *
     * @param string $field
     * @param string $value
     *
     * @return Builder
     */
    public function orWhereContains($field, $value)
    {
        $this->addOrWhere($field, Operator::$contains, $value);

        return $this;
    }

    /**
     * Adds an or where starts with clause to the current query.
     *
     * @param string $field
     * @param string $value
     *
     * @return Builder
     */
    public function orWhereStartsWith($field, $value)
    {
        $this->addOrWhere($field, Operator::$startsWith, $value);

        return $this;
    }

    /**
     * Adds an or where ends with clause to the current query.
     *
     * @param string $field
     * @param string $value
     *
     * @return Builder
     */
    public function orWhereEndsWith($field, $value)
    {
        $this->addOrWhere($field, Operator::$endsWith, $value);

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
        $selects = $this->selects;

        if (count($selects) > 0) {
            // Always make sure object category and distinguished
            // name are included in the selected fields
            $selects[] = ActiveDirectory::OBJECT_CATEGORY;
            $selects[] = ActiveDirectory::DISTINGUISHED_NAME;
        }

        return $selects;
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
     * Adds the inserted field to the selects property.
     *
     * @param string $field
     */
    private function addSelect($field)
    {
        // We'll make sure the field isn't empty
        // before we add it to the selects
        if (!empty($field)) {
            $this->selects[] = $field;
        }
    }

    /**
     * Adds the inserted field, operator and value
     * to the wheres property array.
     *
     * @param string $field
     * @param string $operator
     * @param null   $value
     *
     * @throws InvalidQueryOperatorException
     */
    private function addWhere($field, $operator, $value = null)
    {
        $this->wheres[] = [
            self::$whereFieldKey    => $field,
            self::$whereOperatorKey => $this->getOperator($operator),
            self::$whereValueKey    => Utilities::escape($value),
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
     * @throws InvalidQueryOperatorException
     */
    private function addOrWhere($field, $operator, $value = null)
    {
        $this->orWheres[] = [
            self::$whereFieldKey    => $field,
            self::$whereOperatorKey => $this->getOperator($operator),
            self::$whereValueKey    => Utilities::escape($value),
        ];
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
    private function getOperator($operator)
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
