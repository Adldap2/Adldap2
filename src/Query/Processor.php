<?php

namespace Adldap\Query;

use Adldap\Contracts\Connections\ConnectionInterface;
use Adldap\Contracts\Schemas\SchemaInterface;
use Adldap\Models\Entry;
use Adldap\Objects\Paginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class Processor
{
    /**
     * @var Builder
     */
    protected $builder;

    /**
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * @var SchemaInterface
     */
    protected $schema;

    /**
     * Constructor.
     *
     * @param Builder $builder
     */
    public function __construct(Builder $builder)
    {
        $this->builder = $builder;
        $this->connection = $builder->getConnection();
        $this->schema = $builder->getSchema();
    }

    /**
     * Processes LDAP search results and constructs their model instances.
     *
     * @param resource $results
     *
     * @return array
     */
    public function process($results)
    {
        // Normalize entries. Get entries returns false on failure.
        // We'll always want an array in this situation.
        $entries = ($this->connection->getEntries($results) ?: []);

        if ($this->builder->isRaw()) {
            return $entries;
        }

        $models = [];

        if (Arr::has($entries, 'count')) {
            for ($i = 0; $i < $entries['count']; $i++) {
                $models[] = $this->newLdapEntry($entries[$i]);
            }
        }

        // If the current query isn't paginated,
        // we'll sort the models array here.
        if (!$this->builder->isPaginated()) {
            $models = $this->processSort($models);
        }

        return $models;
    }

    /**
     * Processes paginated LDAP results.
     *
     * @param array $pages
     * @param int   $perPage
     * @param int   $currentPage
     *
     * @return Paginator|bool
     */
    public function processPaginated($pages, $perPage = 50, $currentPage = 0)
    {
        // Make sure we have at least one page of results.
        if (count($pages) > 0) {
            $models = [];

            // Go through each page and process the results into an objects array.
            foreach ($pages as $results) {
                $models = array_merge($models, $this->process($results));
            }

            $models = $this->processSort($models);

            // Return a new Paginator instance.
            return $this->newPaginator($models->toArray(), $perPage, $currentPage, count($pages));
        }

        // Looks like we don't have any results, return false
        return false;
    }

    /**
     * Returns a new LDAP Entry instance.
     *
     * @param array $attributes
     *
     * @return Entry
     */
    public function newLdapEntry(array $attributes = [])
    {
        $attribute = $this->schema->objectClass();

        if (array_key_exists($attribute, $attributes) && array_key_exists(0, $attributes[$attribute])) {
            // Retrieve all of the object classes from the LDAP
            // entry and lowercase them for comparisons.
            $classes = array_map('strtolower', $attributes[$attribute]);

            // Retrieve the model mapping.
            $models = $this->map();

            // Retrieve the object class mappings (with strtolower keys).
            $mappings = array_map('strtolower', array_keys($models));

            // Retrieve the model from the map using the entry's object class.
            $map = array_intersect($mappings, $classes);

            if (count($map) > 0) {
                // Retrieve the objectclass attribute from the map.
                $class = current($map);

                // Retrieve the model from using the object class.
                $model = $models[$class];

                // Construct and return a new model.
                return $this->newModel([], $model)->setRawAttributes($attributes);
            }
        }

        // A default entry model if the object category isn't found.
        return $this->newModel()->setRawAttributes($attributes);
    }

    /**
     * Creates a new model instance.
     *
     * @param array       $attributes
     * @param string|null $model
     *
     * @return mixed|Entry
     */
    public function newModel($attributes = [], $model = null)
    {
        $model = (class_exists($model) ? $model : Entry::class);

        return new $model($attributes, $this->builder);
    }

    /**
     * Returns a new Paginator object instance.
     *
     * @param array $models
     * @param int   $perPage
     * @param int   $currentPage
     * @param int   $pages
     *
     * @return Paginator
     */
    public function newPaginator(array $models, $perPage = 25, $currentPage = 0, $pages = 1)
    {
        return new Paginator($models, $perPage, $currentPage, $pages);
    }

    /**
     * Returns a new doctrine array collection instance.
     *
     * @param array $items
     *
     * @return Collection
     */
    public function newCollection(array $items = [])
    {
        return new Collection($items);
    }

    /**
     * Returns the object category model class mapping.
     *
     * @return array
     */
    public function map()
    {
        return [
            $this->schema->objectClassComputer()    => \Adldap\Models\Computer::class,
            $this->schema->objectClassContact()     => \Adldap\Models\Contact::class,
            $this->schema->objectClassPerson()      => \Adldap\Models\User::class,
            $this->schema->objectClassGroup()       => \Adldap\Models\Group::class,
            $this->schema->objectClassContainer()   => \Adldap\Models\Container::class,
            $this->schema->objectClassPrinter()     => \Adldap\Models\Printer::class,
            $this->schema->objectClassOu()          => \Adldap\Models\OrganizationalUnit::class,
        ];
    }

    /**
     * Sorts LDAP search results.
     *
     * @param array $models
     *
     * @return Collection
     */
    protected function processSort(array $models = [])
    {
        $collection = $this->newCollection($models);

        $field = $this->builder->getSortByField();

        $flags = $this->builder->getSortByFlags();

        $direction = $this->builder->getSortByDirection();

        $desc = ($direction === 'desc' ? true : false);

        return $collection->sortBy(function (Entry $model) use ($field) {
            return $model->getAttribute($field, 0);
        }, $flags, $desc);
    }
}
