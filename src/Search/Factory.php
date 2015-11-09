<?php

namespace Adldap\Search;

use Adldap\Connections\Configuration;
use Adldap\Contracts\Connections\ConnectionInterface;
use Adldap\Contracts\Schemas\SchemaInterface;
use Adldap\Models\AbstractModel;
use Adldap\Query\Builder;
use Adldap\Query\Grammar;
use Adldap\Schemas\Schema;

class Factory
{
    /**
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * @var Configuration
     */
    protected $configuration;

    /**
     * Stores the current query builder instance.
     *
     * @var Builder
     */
    protected $query;

    /**
     * Stores the current schema instance.
     *
     * @var \Adldap\Contracts\Schemas\SchemaInterface
     */
    protected $schema;

    /**
     * Constructor.
     *
     * @param ConnectionInterface $connection
     * @param string              $baseDn
     */
    public function __construct(ConnectionInterface $connection, $baseDn = '')
    {
        $this->connection = $connection;

        $this->setQueryBuilder($this->newQueryBuilder($baseDn));

        $this->setSchema(Schema::get());
    }

    /**
     * Sets the query property.
     *
     * @param Builder $query
     */
    public function setQueryBuilder(Builder $query)
    {
        $this->query = $query;
    }

    /**
     * Sets the schema property.
     *
     * @param SchemaInterface $schema
     */
    public function setSchema(SchemaInterface $schema)
    {
        $this->schema = $schema;
    }

    /**
     * Returns a new query builder instance.
     *
     * @param string $baseDn
     *
     * @return Builder
     */
    public function newQueryBuilder($baseDn = '')
    {
        // Create a new Builder.
        $builder = new Builder($this->connection, $this->newGrammar());

        // Set the Base DN on the Builder.
        $builder->setDn($baseDn);

        // Return the new Builder instance.
        return $builder;
    }

    /**
     * Returns the current query Builder instance.
     *
     * @return Builder
     */
    public function getQueryBuilder()
    {
        return $this->query;
    }

    /**
     * Returns a new query grammar instance.
     *
     * @return Grammar
     */
    public function newGrammar()
    {
        return new Grammar();
    }

    /**
     * Performs a global 'all' search query on the current
     * connection by performing a search for all entries
     * that contain a common name attribute.
     *
     * @return array|bool
     */
    public function all()
    {
        return $this->query->whereHas($this->schema->commonName())->get();
    }

    /**
     * Returns a query builder limited to users.
     *
     * @return Builder
     */
    public function users()
    {
        return $this->query
            ->whereEquals($this->schema->objectCategory(), $this->schema->person());
    }

    /**
     * Returns a query builder limited to printers.
     *
     * @return Builder
     */
    public function printers()
    {
        return $this->query
            ->whereEquals($this->schema->objectClass(), $this->schema->objectClassPrinter());
    }

    /**
     * Returns a query builder limited to organizational units.
     *
     * @return Builder
     */
    public function ous()
    {
        return $this->query
            ->whereEquals($this->schema->objectCategory(), $this->schema->organizationalUnit());
    }

    /**
     * Returns a query builder limited to groups.
     *
     * @return Builder
     */
    public function groups()
    {
        return $this->query
            ->whereEquals($this->schema->objectCategory(), $this->schema->objectCategoryGroup());
    }

    /**
     * Returns a query builder limited to exchange servers.
     *
     * @return Builder|false
     */
    public function exchangeServers()
    {
        $namingContext = $this->getConfigurationNamingContext();

        if (is_string($namingContext)) {
            return $this->query
                ->setDn($namingContext)
                ->whereEquals($this->schema->objectCategory(), $this->schema->objectCategoryExchangeServer());
        }

        return false;
    }

    /**
     * Returns a query builder limited to exchange servers.
     *
     * @return Builder
     */
    public function containers()
    {
        return $this->query
            ->whereEquals($this->schema->objectCategory(), $this->schema->objectCategoryContainer());
    }

    /**
     * Returns a query builder limited to exchange servers.
     *
     * @return Builder
     */
    public function contacts()
    {
        return $this->query
            ->whereEquals($this->schema->objectClass(), $this->schema->contact());
    }

    /**
     * Returns a query builder limited to exchange servers.
     *
     * @return Builder
     */
    public function computers()
    {
        return $this->query
            ->whereEquals($this->schema->objectCategory(), $this->schema->objectCategoryComputer());
    }

    /**
     * Returns the current configuration naming context of the current domain.
     *
     * @return bool|string
     */
    protected function getConfigurationNamingContext()
    {
        $result = $this->getRootDse();

        if ($result instanceof AbstractModel) {
            return $result->getAttribute($this->schema->configurationNamingContext(), 0);
        }

        return false;
    }

    /**
     * Get the RootDSE properties from a domain controller.
     *
     * @return AbstractModel|bool
     */
    protected function getRootDse()
    {
        return $this->query
            ->setDn(null)
            ->read(true)
            ->whereHas($this->schema->objectClass())
            ->first();
    }

    /**
     * Handle dynamic method calls on the search object.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array([$this->query, $method], $parameters);
    }
}
