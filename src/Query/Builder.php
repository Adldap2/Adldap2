<?php

namespace Adldap\Query;

use Adldap\Contracts\Connections\ConnectionInterface;
use Adldap\Contracts\Schemas\SchemaInterface;
use Adldap\Exceptions\ModelNotFoundException;
use Adldap\Models\Entry;
use Adldap\Objects\Paginator;
use Adldap\Query\Bindings\AbstractBinding;
use Adldap\Query\Bindings\Filter;
use Adldap\Query\Bindings\OrWhere;
use Adldap\Query\Bindings\Select;
use Adldap\Query\Bindings\Where;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class Builder
{
    /**
     * Stores the bool to determine whether or
     * not the current query is paginated.
     *
     * @var bool
     */
    public $paginated = false;

    /**
     * The query bindings.
     *
     * @var array
     */
    protected $bindings = [
        'select'    => [],
        'where'     => [],
        'orWhere'   => [],
        'filter'    => [],
    ];

    /**
     * Stores the field to sort search results by.
     *
     * @var string
     */
    protected $sortByField = '';

    /**
     * Stores the direction to sort the results by.
     *
     * @var string
     */
    protected $sortByDirection = '';

    /**
     * The distinguished name to perform searches upon.
     *
     * @var string|null
     */
    protected $dn;

    /**
     * Stores the bool to determine whether or not
     * to search LDAP recursively.
     *
     * @var bool
     */
    protected $recursive = true;

    /**
     * Stores the bool to determine whether or not
     * to search LDAP on the base scope.
     *
     * @var bool
     */
    protected $read = false;

    /**
     * Stores the bool to determine whether or not
     * to return LDAP results in their raw format.
     *
     * @var bool
     */
    protected $raw = false;

    /**
     * Stores the current connection instance.
     *
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * Stores the current grammar instance.
     *
     * @var Grammar
     */
    protected $grammar;

    /**
     * Stores the current schema instance.
     *
     * @var SchemaInterface
     */
    protected $schema;

    /**
     * Constructor.
     *
     * @param ConnectionInterface $connection
     * @param Grammar             $grammar
     * @param SchemaInterface     $schema
     */
    public function __construct(ConnectionInterface $connection, Grammar $grammar, SchemaInterface $schema)
    {
        $this->setConnection($connection);
        $this->setGrammar($grammar);
        $this->setSchema($schema);
    }

    /**
     * Sets the current connection.
     *
     * @param ConnectionInterface $connection
     */
    public function setConnection(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Sets the current filter grammar.
     *
     * @param Grammar $grammar
     */
    public function setGrammar(Grammar $grammar)
    {
        $this->grammar = $grammar;
    }

    /**
     * Sets the current schema.
     *
     * @param SchemaInterface $schema
     */
    public function setSchema(SchemaInterface $schema)
    {
        $this->schema = $schema;
    }

    /**
     * Returns the current schema.
     *
     * @return SchemaInterface
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * Returns a new Query Builder instance.
     *
     * @return Builder
     */
    public function newInstance()
    {
        $new = new static($this->connection, $this->grammar, $this->schema);

        $new->setDn($this->getDn());

        return $new;
    }

    /**
     * Returns the current query.
     *
     * @return array|\Illuminate\Support\Collection
     */
    public function get()
    {
        return $this->query($this->getQuery());
    }

    /**
     * Compiles and returns the current query string.
     *
     * @return string
     */
    public function getQuery()
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
     * Returns the current Connection instance.
     *
     * @return ConnectionInterface
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Returns the builders DN to perform
     * searches upon.
     *
     * @return string
     */
    public function getDn()
    {
        return $this->dn;
    }

    /**
     * Sets the DN to perform searches upon.
     *
     * @param string|null $dn
     *
     * @return Builder
     */
    public function setDn($dn = null)
    {
        $this->dn = $dn;

        return $this;
    }

    /**
     * Performs the specified query on the current LDAP connection.
     *
     * @param string $query
     *
     * @return array|\Illuminate\Support\Collection
     */
    public function query($query)
    {
        $dn = $this->getDn();

        $selects = $this->getSelects();

        if ($this->read) {
            // If read is true, we'll perform a read search, retrieving one record
            $results = $this->connection->read($dn, $query, $selects);
        } elseif ($this->recursive) {
            // If recursive is true, we'll perform a recursive search
            $results = $this->connection->search($dn, $query, $selects);
        } else {
            // Read and recursive is false, we'll return a listing
            $results = $this->connection->listing($dn, $query, $selects);
        }

        return $this->process($results);
    }

    /**
     * Paginates the current LDAP query.
     *
     * @param int  $perPage
     * @param int  $currentPage
     * @param bool $isCritical
     *
     * @return Paginator|bool
     */
    public function paginate($perPage = 50, $currentPage = 0, $isCritical = true)
    {
        // Set the current query to paginated
        $this->paginated = true;

        // Stores all LDAP entries in a page array
        $pages = [];

        $cookie = '';

        do {
            $this->connection->controlPagedResult($perPage, $isCritical, $cookie);

            $results = $this->connection->search($this->getDn(), $this->getQuery(), $this->getSelects());

            if ($results) {
                $this->connection->controlPagedResultResponse($results, $cookie);

                // We'll collect the results into the pages array.
                $pages[] = $results;
            }
        } while ($cookie !== null && !empty($cookie));

        if (count($pages) > 0) {
            return $this->processPaginated($pages, $perPage, $currentPage);
        }

        return false;
    }

    /**
     * Returns the first entry in a search result.
     *
     * @param array|string $columns
     *
     * @return Entry|false
     */
    public function first($columns = [])
    {
        $results = $this->select($columns)->get();

        if ($results instanceof Collection) {
            return $results->first();
        } elseif (is_array($results) && array_key_exists(0, $results)) {
            return $results[0];
        }

        // No entries were returned, return false
        return false;
    }

    /**
     * Returns the first entry in a search result.
     *
     * If no entry is found, an exception is thrown.
     *
     * @param array|string $columns
     *
     * @throws ModelNotFoundException
     *
     * @return Entry|bool
     */
    public function firstOrFail($columns = [])
    {
        $record = $this->first($columns);

        if ($record === false || is_null($record)) {
            throw new ModelNotFoundException('Unable to find record in Active Directory.');
        }

        return $record;
    }

    /**
     * Finds a record by the specified attribute and value.
     *
     * @param string       $attribute
     * @param string       $value
     * @param array|string $columns
     *
     * @return Entry|bool
     */
    public function findBy($attribute, $value, $columns = [])
    {
        return $this->whereEquals($attribute, $value)->first($columns);
    }

    /**
     * Finds a record by the specified attribute and value.
     *
     * If no record is found an exception is thrown.
     *
     * @param string       $attribute
     * @param string       $value
     * @param array|string $columns
     *
     * @throws ModelNotFoundException
     *
     * @return Entry|bool
     */
    public function findByOrFail($attribute, $value, $columns = [])
    {
        return $this->whereEquals($attribute, $value)->firstOrFail($columns);
    }

    /**
     * Finds a record using ambiguous name resolution.
     *
     * @param string       $anr
     * @param array|string $columns
     *
     * @return Entry|bool
     */
    public function find($anr, $columns = [])
    {
        return $this->findBy($this->schema->anr(), $anr, $columns);
    }

    /**
     * Finds a record using ambiguous name resolution. If a record
     * is not found, an exception is thrown.
     *
     * @param string       $anr
     * @param array|string $columns
     *
     * @throws ModelNotFoundException
     *
     * @return Entry|bool
     */
    public function findOrFail($anr, $columns = [])
    {
        $entry = $this->find($anr, $columns);

        // Make sure we check if the result is an entry or an array before
        // we throw an exception in case the user wants raw results.
        if (!$entry instanceof Entry && !is_array($entry)) {
            throw new ModelNotFoundException('Unable to find record in Active Directory.');
        }

        return $entry;
    }

    /**
     * Finds a record by its distinguished name.
     *
     * @param string       $dn
     * @param array|string $columns
     *
     * @return bool|Entry
     */
    public function findByDn($dn, $columns = [])
    {
        return $this
            ->setDn($dn)
            ->read(true)
            ->whereHas($this->schema->objectClass())
            ->first($columns);
    }

    /**
     * Finds a record by its distinguished name.
     *
     * Fails upon no records returned.
     *
     * @param string       $dn
     * @param array|string $columns
     *
     * @throws ModelNotFoundException
     *
     * @return bool|Entry
     */
    public function findByDnOrFail($dn, $columns = [])
    {
        return $this
            ->setDn($dn)
            ->read(true)
            ->whereHas($this->schema->objectClass())
            ->firstOrFail($columns);
    }

    /**
     * Finds a record by its Object SID.
     *
     * @param string       $sid
     * @param array|string $columns
     *
     * @return bool|Entry
     */
    public function findBySid($sid, $columns = [])
    {
        return $this->findBy($this->schema->objectSid(), $sid, $columns);
    }

    /**
     * Finds the Base DN of your domain controller.
     *
     * @return string|bool
     */
    public function findBaseDn()
    {
        $schema = $this->schema;

        $result = $this
            ->setDn(null)
            ->read()
            ->raw()
            ->whereHas($schema->objectClass())
            ->first();

        $key = $schema->defaultNamingContext();

        if (is_array($result) && array_key_exists($key, $result)) {
            if (array_key_exists(0, $result[$key])) {
                return $result[$key][0];
            }
        }

        return false;
    }

    /**
     * Adds the inserted fields to query on the current LDAP connection.
     *
     * @param array|string $fields
     *
     * @return Builder
     */
    public function select($fields = [])
    {
        $fields = is_array($fields) ? $fields : func_get_args();

        foreach ($fields as $field) {
            $this->addBinding(new Select($field), 'select');
        }

        return $this;
    }

    /**
     * Adds a raw filter to the current query.
     *
     * @param array|string $filters
     *
     * @return Builder
     */
    public function rawFilter($filters = [])
    {
        $filters = is_array($filters) ? $filters : func_get_args();

        foreach ($filters as $filter) {
            $this->addBinding(new Filter($filter), 'filter');
        }

        return $this;
    }

    /**
     * Adds a where clause to the current query.
     *
     * @param string $field
     * @param string $operator
     * @param string $value
     * @param string $type
     *
     * @return Builder
     */
    public function where($field, $operator = null, $value = null, $type = 'where')
    {
        if (is_array($field)) {
            // If the column is an array, we will assume it is an array of
            // key-value pairs and can add them each as a where clause.
            foreach ($field as $key => $value) {
                $this->where($key, Operator::$equals, $value, $type);
            }

            return $this;
        }

        // We'll bypass the has and notHas operator since they
        // only require two arguments inside the where method.
        $bypass = [Operator::$has, Operator::$notHas];

        // Here we will make some assumptions about the operator. If only 2 values are
        // passed to the method, we will assume that the operator is an equals sign
        // and keep going.
        if (func_num_args() === 2 && in_array($operator, $bypass) === false) {
            list($value, $operator) = [$operator, '='];
        }

        // We'll construct a new where binding.
        $binding = $this->newWhereBinding($field, $operator, $value, $type);

        // Then add it to the current query builder.
        $this->addBinding($binding, $type);

        return $this;
    }

    /**
     * Adds a where equals clause to the current query.
     *
     * @param string $field
     * @param string $value
     *
     * @return Builder
     */
    public function whereEquals($field, $value)
    {
        $this->where($field, Operator::$equals, $value);

        return $this;
    }

    /**
     * Adds a where approximately equals clause to the current query.
     *
     * @param string $field
     * @param string $value
     *
     * @return Builder
     */
    public function whereApproximatelyEquals($field, $value)
    {
        $this->where($field, Operator::$approximatelyEquals, $value);

        return $this;
    }

    /**
     * Adds a where has clause to the current query.
     *
     * @param string $field
     *
     * @return Builder
     */
    public function whereHas($field)
    {
        $this->where($field, Operator::$has);

        return $this;
    }

    /**
     * Adds a where not has clause to the current query.
     *
     * @param string $field
     *
     * @return Builder
     */
    public function whereNotHas($field)
    {
        $this->where($field, Operator::$notHas);

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
        $this->where($field, Operator::$contains, $value);

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
    public function whereNotContains($field, $value)
    {
        $this->where($field, Operator::$notContains, $value);

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
        $this->where($field, Operator::$startsWith, $value);

        return $this;
    }

    /**
     * Adds a where *not* starts with clause to the current query.
     *
     * @param string $field
     * @param string $value
     *
     * @return Builder
     */
    public function whereNotStartsWith($field, $value)
    {
        $this->where($field, Operator::$notStartsWith, $value);

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
        $this->where($field, Operator::$endsWith, $value);

        return $this;
    }

    /**
     * Adds a enabled filter to the current query.
     *
     * @return Builder
     */
    public function whereEnabled()
    {
        $this->rawFilter('(!(UserAccountControl:1.2.840.113556.1.4.803:=2))');

        return $this;
    }

    /**
     * Adds a disabled filter to the current query.
     *
     * @return Builder
     */
    public function whereDisabled()
    {
        $this->rawFilter('(UserAccountControl:1.2.840.113556.1.4.803:=2)');

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
        return $this->where($field, $operator, $value, 'orWhere');
    }

    /**
     * Adds an or where has clause to the current query.
     *
     * @param string $field
     *
     * @return Builder
     */
    public function orWhereHas($field)
    {
        $this->orWhere($field, Operator::$has);

        return $this;
    }

    /**
     * Adds a where not has clause to the current query.
     *
     * @param string $field
     *
     * @return Builder
     */
    public function orWhereNotHas($field)
    {
        $this->orWhere($field, Operator::$notHas);

        return $this;
    }

    /**
     * Adds an or where equals clause to the current query.
     *
     * @param string $field
     * @param string $value
     *
     * @return Builder
     */
    public function orWhereEquals($field, $value)
    {
        $this->orWhere($field, Operator::$equals, $value);

        return $this;
    }

    /**
     * Adds a or where approximately equals clause to the current query.
     *
     * @param string $field
     * @param string $value
     *
     * @return Builder
     */
    public function orWhereApproximatelyEquals($field, $value)
    {
        $this->orWhere($field, Operator::$approximatelyEquals, $value);

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
        $this->orWhere($field, Operator::$contains, $value);

        return $this;
    }

    /**
     * Adds an or where *not* contains clause to the current query.
     *
     * @param string $field
     * @param string $value
     *
     * @return Builder
     */
    public function orWhereNotContains($field, $value)
    {
        $this->orWhere($field, Operator::$notContains, $value);

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
        $this->orWhere($field, Operator::$startsWith, $value);

        return $this;
    }

    /**
     * Adds an or where *not* starts with clause to the current query.
     *
     * @param string $field
     * @param string $value
     *
     * @return Builder
     */
    public function orWhereNotStartsWith($field, $value)
    {
        $this->orWhere($field, Operator::$notStartsWith, $value);

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
        $this->orWhere($field, Operator::$endsWith, $value);

        return $this;
    }

    /**
     * Adds an or where *not* ends with clause to the current query.
     *
     * @param string $field
     * @param string $value
     *
     * @return Builder
     */
    public function orWhereNotEndsWith($field, $value)
    {
        $this->orWhere($field, Operator::$notEndsWith, $value);

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
        if (count($this->getSelects()) > 0) {
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
        $selects = $this->bindings['select'];

        $schema = $this->schema;

        if (count($selects) > 0) {
            // Always make sure object category, class, and distinguished
            // name are included in the selected fields.
            $selects[] = new Select($schema->objectCategory());
            $selects[] = new Select($schema->objectClass());
            $selects[] = new Select($schema->distinguishedName());
        }

        return $selects;
    }

    /**
     * Returns the filters on the current builder.
     *
     * @return array
     */
    public function getFilters()
    {
        return $this->bindings['filter'];
    }

    /**
     * Returns the wheres on the current builder.
     *
     * @return array
     */
    public function getWheres()
    {
        return $this->bindings['where'];
    }

    /**
     * Returns the or wheres on the current builder.
     *
     * @return array
     */
    public function getOrWheres()
    {
        return $this->bindings['orWhere'];
    }

    /**
     * Sorts the LDAP search results by the
     * specified field and direction.
     *
     * @param string $field
     * @param string $direction
     *
     * @return Builder
     */
    public function sortBy($field, $direction = 'asc')
    {
        $this->sortByField = $field;

        // Lowercase direction for comparisons.
        $direction = strtolower($direction);

        if ($direction === 'asc' || $direction === 'desc') {
            $this->sortByDirection = $direction;
        }

        return $this;
    }

    /**
     * Sets the recursive property to tell the search
     * whether or not to search recursively.
     *
     * @param bool $recursive
     *
     * @return Builder
     */
    public function recursive($recursive = true)
    {
        $this->recursive = (bool) $recursive;

        return $this;
    }

    /**
     * Sets the recursive property to tell the search
     * whether or not to search on the base scope and
     * return a single entry.
     *
     * @param bool $read
     *
     * @return Builder
     */
    public function read($read = true)
    {
        $this->read = (bool) $read;

        return $this;
    }

    /**
     * Sets the recursive property to tell the search
     * whether or not to return the LDAP results in
     * their raw format.
     *
     * @param bool $raw
     *
     * @return Builder
     */
    public function raw($raw = true)
    {
        $this->raw = (bool) $raw;

        return $this;
    }

    /**
     * Returns the query builders sort by field.
     *
     * @return string
     */
    public function getSortByField()
    {
        return $this->sortByField;
    }

    /**
     * Returns the query builders sort by direction.
     *
     * @return string
     */
    public function getSortByDirection()
    {
        return $this->sortByDirection;
    }

    /**
     * Returns bool that determines whether the current
     * query builder will return raw results.
     *
     * @return bool
     */
    public function isRaw()
    {
        return $this->raw;
    }

    /**
     * Returns bool that determines whether the current
     * query builder will return paginated results.
     *
     * @return bool
     */
    public function isPaginated()
    {
        return $this->paginated;
    }

    /**
     * Returns bool that determines whether the current
     * query builder will return sorted results.
     *
     * @return bool
     */
    public function isSorted()
    {
        return $this->sortByField ? true : false;
    }

    /**
     * Adds a binding to the current query.
     *
     * @param AbstractBinding $value
     * @param string          $type
     *
     * @throws InvalidArgumentException
     *
     * @return Builder
     */
    public function addBinding(AbstractBinding $value, $type = 'where')
    {
        if (!array_key_exists($type, $this->bindings)) {
            throw new InvalidArgumentException("Invalid binding type: {$type}.");
        }

        $this->bindings[$type][] = $value;

        return $this;
    }

    /**
     * Clears all query bindings.
     *
     * @return Builder
     */
    public function clearBindings()
    {
        foreach ($this->bindings as $key => $bindings) {
            $this->bindings[$key] = [];
        }

        return $this;
    }

    /**
     * Handle dynamic method calls on the query builder
     * object to be directed to the query processor.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        $processor = $this->newProcessor();

        return call_user_func_array([$processor, $method], $parameters);
    }

    /**
     * Returns a new query Processor instance.
     *
     * @return Processor
     */
    protected function newProcessor()
    {
        return new Processor($this);
    }

    /**
     * Constructs a new where binding depending on the specified type.
     *
     * @param string      $field
     * @param string      $operator
     * @param string|null $value
     * @param string      $type
     *
     * @throws InvalidArgumentException
     *
     * @return Where|OrWhere
     */
    protected function newWhereBinding($field, $operator, $value = null, $type = 'where')
    {
        switch (strtolower($type)) {
            case 'where':
                return new Where($field, $operator, $value);
            case 'orwhere':
                return new OrWhere($field, $operator, $value);
            default:
                throw new InvalidArgumentException("Invalid binding type: $type.");
        }
    }
}
