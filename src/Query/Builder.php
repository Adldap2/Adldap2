<?php

namespace Adldap\Query;

use InvalidArgumentException;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Adldap\Utilities;
use Adldap\Models\Model;
use Adldap\Objects\Paginator;
use Adldap\Query\Bindings\Filter;
use Adldap\Query\Bindings\Where;
use Adldap\Query\Bindings\Select;
use Adldap\Query\Bindings\OrWhere;
use Adldap\Query\Bindings\AbstractBinding;
use Adldap\Schemas\ActiveDirectory;
use Adldap\Exceptions\ModelNotFoundException;
use Adldap\Contracts\Schemas\SchemaInterface;
use Adldap\Contracts\Connections\ConnectionInterface;

class Builder
{
    /**
     * Determines whether the current query is paginated.
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
     * The field to sort search results by.
     *
     * @var string
     */
    protected $sortByField = '';

    /**
     * The direction to sort the results by.
     *
     * @var string
     */
    protected $sortByDirection = '';

    /**
     * The sort flags for sorting query results.
     *
     * @var int
     */
    protected $sortByFlags;

    /**
     * The distinguished name to perform searches upon.
     *
     * @var string|null
     */
    protected $dn;

    /**
     * Determines whether or not to search LDAP recursively.
     *
     * @var bool
     */
    protected $recursive = true;

    /**
     * Determines whether or not to search LDAP on the base scope.
     *
     * @var bool
     */
    protected $read = false;

    /**
     * Determines whether or not to return LDAP results in their raw array format.
     *
     * @var bool
     */
    protected $raw = false;

    /**
     * The current connection instance.
     *
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * The current grammar instance.
     *
     * @var Grammar
     */
    protected $grammar;

    /**
     * The current schema instance.
     *
     * @var SchemaInterface
     */
    protected $schema;

    /**
     * Constructor.
     *
     * @param ConnectionInterface  $connection
     * @param Grammar              $grammar
     * @param SchemaInterface|null $schema
     */
    public function __construct(ConnectionInterface $connection, Grammar $grammar, SchemaInterface $schema = null)
    {
        $this->setConnection($connection)
            ->setGrammar($grammar)
            ->setSchema($schema);
    }

    /**
     * Sets the current connection.
     *
     * @param ConnectionInterface $connection
     *
     * @return Builder
     */
    public function setConnection(ConnectionInterface $connection)
    {
        $this->connection = $connection;

        return $this;
    }

    /**
     * Sets the current filter grammar.
     *
     * @param Grammar $grammar
     *
     * @return Builder
     */
    public function setGrammar(Grammar $grammar)
    {
        $this->grammar = $grammar;

        return $this;
    }

    /**
     * Sets the current schema.
     *
     * @param SchemaInterface|null $schema
     *
     * @return Builder
     */
    public function setSchema(SchemaInterface $schema = null)
    {
        $this->schema = $schema ?: new ActiveDirectory();

        return $this;
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
        // We'll set the base DN of the new Builder so
        // developers don't need to do this manually.
        return (new static($this->connection, $this->grammar, $this->schema))
            ->setDn($this->getDn());
    }

    /**
     * Returns the current query.
     *
     * @return \Illuminate\Support\Collection|array
     */
    public function get()
    {
        // We'll mute any warnings / errors here. We just need to
        // know if any query results were returned.
        return @$this->query($this->getQuery());
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
     * Returns the unescaped query.
     *
     * @return string
     */
    public function getUnescapedQuery()
    {
        return Utilities::unescape($this->getQuery());
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
     * Returns the builders DN to perform searches upon.
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
        $this->dn = (string) $dn;

        return $this;
    }

    /**
     * Alias for setting the base DN of the query.
     *
     * @param string $dn
     *
     * @return Builder
     */
    public function in($dn)
    {
        return $this->setDn($dn);
    }

    /**
     * Performs the specified query on the current LDAP connection.
     *
     * @param string $query
     *
     * @return \Illuminate\Support\Collection|array
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
        $this->paginated = true;

        $pages = [];

        $cookie = '';

        do {
            $this->connection->controlPagedResult($perPage, $isCritical, $cookie);

            // Run the search.
            $resource = $this->connection->search($this->getDn(), $this->getQuery(), $this->getSelects());

            if ($resource) {
                $this->connection->controlPagedResultResponse($resource, $cookie);

                // We'll collect each resource result into the pages array.
                $pages[] = $resource;
            }
        } while (!empty($cookie));

        if (count($pages) > 0) {
            // If we have at least one page, we can process the results.
            $paginator = $this->processPaginated($pages, $perPage, $currentPage);

            // Reset paged result on the current connection. We won't pass in the current $perPage
            // parameter since we want to reset the page size to the default '1000'. Sending '0'
            // eliminates any further opportunity for running queries in the same request,
            // even though that is supposed to be the correct usage.
            $this->connection->controlPagedResult();

            return $paginator;
        }

        return false;
    }

    /**
     * Returns the first entry in a search result.
     *
     * @param array|string $columns
     *
     * @return Model|array|null
     */
    public function first($columns = [])
    {
        $results = $this->select($columns)->get();

        if ($results instanceof Collection) {
            return $results->first();
        } elseif (is_array($results) && array_key_exists(0, $results)) {
            return $results[0];
        }
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
     * @return Model
     */
    public function firstOrFail($columns = [])
    {
        $record = $this->first($columns);

        if (!$record) {
            throw (new ModelNotFoundException())
                ->setQuery($this->getUnescapedQuery(), $this->getDn());
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
     * @return mixed
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
     * @return mixed
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
     * @return mixed
     */
    public function find($anr, $columns = [])
    {
        return $this->findBy($this->schema->anr(), $anr, $columns);
    }

    /**
     * Finds multiple records using ambiguous name resolution.
     *
     * @param array $anrs
     * @param array $columns
     *
     * @return mixed
     */
    public function findMany(array $anrs = [], $columns = [])
    {
        return $this->findManyBy($this->schema->anr(), $anrs, $columns);
    }

    /**
     * Finds many records by the specified attribute.
     *
     * @param string $attribute
     * @param array  $values
     * @param array  $columns
     *
     * @return mixed
     */
    public function findManyBy($attribute, array $values = [], $columns = [])
    {
        $models = [];

        foreach ($values as $value) {
            $model = $this->newInstance()->findBy($attribute, $value, $columns);

            if ($model instanceof Model) {
                $models[] = $model;
            }
        }

        return $this->newCollection($models);
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
     * @return mixed
     */
    public function findOrFail($anr, $columns = [])
    {
        $entry = $this->find($anr, $columns);

        // Make sure we check if the result is an entry or an array before
        // we throw an exception in case the user wants raw results.
        if (!$entry instanceof Model && !is_array($entry)) {
            throw (new ModelNotFoundException())
                ->setQuery($this->getUnescapedQuery(), $this->getDn());
        }

        return $entry;
    }

    /**
     * Finds a record by its distinguished name.
     *
     * @param string|array $dn
     * @param array|string $columns
     *
     * @return bool|Model
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
     * @return Model
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
     * @return mixed
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
     * @param string|array $field
     * @param string       $operator
     * @param string       $value
     * @param string       $type
     *
     * @return Builder
     */
    public function where($field, $operator = null, $value = null, $type = 'and')
    {
        if (is_array($field)) {
            // If the column is an array, we will assume it is an array of
            // key-value pairs and can add them each as a where clause.
            foreach ($field as $key => $value) {
                $this->where($key, Operator::$equals, $value, $type);
            }

            return $this;
        }

        // We'll bypass the 'has' and 'notHas' operator since they
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

        // Normalize type.
        $type = ($type == 'or' ? 'orWhere' : 'where');

        // Then add it to the current query builder.
        return $this->addBinding($binding, $type);
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
        return $this->where($field, Operator::$equals, $value);
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
        return $this->where($field, Operator::$approximatelyEquals, $value);
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
        return $this->where($field, Operator::$has);
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
        return $this->where($field, Operator::$notHas);
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
        return $this->where($field, Operator::$contains, $value);
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
        return $this->where($field, Operator::$notContains, $value);
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
        return $this->where($field, Operator::$startsWith, $value);
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
        return $this->where($field, Operator::$notStartsWith, $value);
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
        return $this->where($field, Operator::$endsWith, $value);
    }

    /**
     * Adds a enabled filter to the current query.
     *
     * @return Builder
     */
    public function whereEnabled()
    {
        return $this->rawFilter('(!(UserAccountControl:1.2.840.113556.1.4.803:=2))');
    }

    /**
     * Adds a disabled filter to the current query.
     *
     * @return Builder
     */
    public function whereDisabled()
    {
        return $this->rawFilter('(UserAccountControl:1.2.840.113556.1.4.803:=2)');
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
        return $this->where($field, $operator, $value, 'or');
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
        return $this->orWhere($field, Operator::$has);
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
        return $this->orWhere($field, Operator::$notHas);
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
        return $this->orWhere($field, Operator::$equals, $value);
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
        return $this->orWhere($field, Operator::$approximatelyEquals, $value);
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
        return $this->orWhere($field, Operator::$contains, $value);
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
        return $this->orWhere($field, Operator::$notContains, $value);
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
        return $this->orWhere($field, Operator::$startsWith, $value);
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
        return $this->orWhere($field, Operator::$notStartsWith, $value);
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
        return $this->orWhere($field, Operator::$endsWith, $value);
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
        return $this->orWhere($field, Operator::$notEndsWith, $value);
    }

    /**
     * Returns true / false depending if the current object
     * contains selects.
     *
     * @return bool
     */
    public function hasSelects()
    {
        return count($this->getSelects()) > 0;
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
            // Always make sure object category and class are selected. We need these
            // attributes to construct the right model instance for the record.
            $selects[] = new Select($schema->objectCategory());
            $selects[] = new Select($schema->objectClass());
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
     * Returns all of the query builder bindings.
     *
     * @return array
     */
    public function getBindings()
    {
        return $this->bindings;
    }

    /**
     * Sorts the LDAP search results by the
     * specified field and direction.
     *
     * @param string   $field
     * @param string   $direction
     * @param int|null $flags
     *
     * @return Builder
     */
    public function sortBy($field, $direction = 'asc', $flags = null)
    {
        $this->sortByField = $field;

        // Lowercase direction for comparisons.
        $direction = strtolower($direction);

        if ($direction === 'asc' || $direction === 'desc') {
            $this->sortByDirection = $direction;
        }

        if (is_null($flags)) {
            $flags = SORT_NATURAL + SORT_FLAG_CASE;
        }

        $this->sortByFlags = $flags;

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
     * Returns the query builders sort by flags.
     *
     * @return int
     */
    public function getSortByFlags()
    {
        return $this->sortByFlags;
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
        if (Str::startsWith($method, 'where')) {
            return $this->dynamicWhere($method, $parameters);
        }

        return call_user_func_array([$this->newProcessor(), $method], $parameters);
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
    protected function newWhereBinding($field, $operator, $value = null, $type = 'and')
    {
        switch (strtolower($type)) {
            case 'and':
                return new Where($field, $operator, $value);
            case 'or':
                return new OrWhere($field, $operator, $value);
            default:
                throw new InvalidArgumentException("Invalid binding type: {$type}.");
        }
    }

    /**
     * Handles dynamic "where" clauses to the query.
     *
     * @param string $method
     * @param string $parameters
     *
     * @return $this
     */
    public function dynamicWhere($method, $parameters)
    {
        $finder = substr($method, 5);

        $segments = preg_split('/(And|Or)(?=[A-Z])/', $finder, -1, PREG_SPLIT_DELIM_CAPTURE);

        // The connector variable will determine which connector will be used for the
        // query condition. We will change it as we come across new boolean values
        // in the dynamic method strings, which could contain a number of these.
        $connector = 'and';

        $index = 0;

        foreach ($segments as $segment) {
            // If the segment is not a boolean connector, we can assume it is a column's name
            // and we will add it to the query as a new constraint as a where clause, then
            // we can keep iterating through the dynamic method string's segments again.
            if ($segment != 'And' && $segment != 'Or') {
                $this->addDynamic($segment, $connector, $parameters, $index);

                $index++;
            }

            // Otherwise, we will store the connector so we know how the next where clause we
            // find in the query should be connected to the previous ones, meaning we will
            // have the proper boolean connector to connect the next where clause found.
            else {
                $connector = $segment;
            }
        }

        return $this;
    }

    /**
     * Add a single dynamic where clause statement to the query.
     *
     * @param string $segment
     * @param string $connector
     * @param array  $parameters
     * @param int    $index
     *
     * @return void
     */
    protected function addDynamic($segment, $connector, $parameters, $index)
    {
        // Once we have parsed out the columns and formatted the boolean operators we
        // are ready to add it to this query as a where clause just like any other
        // clause on the query. Then we'll increment the parameter index values.
        $bool = strtolower($connector);

        $this->where(Str::snake($segment), '=', $parameters[$index], $bool);
    }
}
