<?php

namespace Adldap\Query;

use Adldap\Contracts\Connections\ConnectionInterface;
use Adldap\Contracts\Schemas\SchemaInterface;
use Adldap\Models\Entry;
use Adldap\Objects\Paginator;
use Adldap\Utilities;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;

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
        $entries = $this->connection->getEntries($results);

        if ($this->builder->isRaw() === true) {
            return $entries;
        } else {
            $models = [];

            if (is_array($entries) && array_key_exists('count', $entries)) {
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
                $processed = $this->process($results);

                $models = array_merge($models, $processed);
            }

            $models = $this->processSort($models);

            // Return a new Paginator instance.
            return $this->newPaginator($models, $perPage, $currentPage, count($pages));
        }

        // Looks like we don't have any results, return false
        return false;
    }

    /**
     * Sorts LDAP search results.
     *
     * @param array $models
     *
     * @return array
     */
    private function processSort(array $models = [])
    {
        $collection = $this->newCollection($models);

        $sort = [$this->builder->getSortByField() => $this->builder->getSortByDirection()];

        $criteria = (new Criteria())->orderBy($sort);

        return $collection->matching($criteria)->toArray();
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
        $attribute = $this->schema->objectCategory();

        if (array_key_exists($attribute, $attributes) && array_key_exists(0, $attributes[$attribute])) {
            // We'll explode the DN so we can grab it's object category.
            $category = Utilities::explodeDn($attributes[$attribute][0]);
            // Make sure the category string exists in the attribute array.
            if (array_key_exists(0, $category)) {
                $category = strtolower($category[0]);

                $mappings = $this->map();

                // Retrieve the category model mapping.
                if (array_key_exists($category, $mappings)) {
                    $model = $mappings[$category];

                    return $this->newModel([], $model)->setRawAttributes($attributes);
                }
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
        if (!is_null($model) && class_exists($model)) {
            return new $model($attributes, $this->builder);
        }

        return new Entry($attributes, $this->builder);
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
     * @param array $elements
     *
     * @return ArrayCollection
     */
    public function newCollection(array $elements = [])
    {
        return new ArrayCollection($elements);
    }

    /**
     * Returns the object category model class mapping.
     *
     * @return array
     */
    public function map()
    {
        return [
            $this->schema->objectCategoryComputer()           => 'Adldap\Models\Computer',
            $this->schema->objectCategoryPerson()             => 'Adldap\Models\User',
            $this->schema->objectCategoryGroup()              => 'Adldap\Models\Group',
            $this->schema->objectCategoryExchangeServer()     => 'Adldap\Models\ExchangeServer',
            $this->schema->objectCategoryContainer()          => 'Adldap\Models\Container',
            $this->schema->objectCategoryPrinter()            => 'Adldap\Models\Printer',
            $this->schema->objectCategoryOrganizationalUnit() => 'Adldap\Models\OrganizationalUnit',
        ];
    }
}
