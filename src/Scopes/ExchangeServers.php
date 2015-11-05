<?php

namespace Adldap\Scopes;

use Adldap\Models\Entry;
use Adldap\Schemas\ActiveDirectory;
use Adldap\Schemas\Schema;

class ExchangeServers extends AbstractScope implements QueryableInterface
{
    /**
     * Finds an exchange server.
     *
     * @param string $name
     * @param array  $fields
     *
     * @return array|bool
     */
    public function find($name, $fields = [])
    {
        $namingContext = $this->getConfigurationNamingContext();

        if (is_string($namingContext)) {
            return $this->search()
                ->setDn($namingContext)
                ->select($fields)
                ->find($name);
        }

        return false;
    }

    /**
     * Returns all exchange servers.
     *
     * @param array  $fields
     * @param bool   $sorted
     * @param string $sortBy
     * @param string $sortDirection
     *
     * @return \Doctrine\Common\Collections\ArrayCollection|bool
     */
    public function all($fields = [], $sorted = true, $sortBy = ActiveDirectory::COMMON_NAME, $sortDirection = 'asc')
    {
        $namingContext = $this->getConfigurationNamingContext();

        if (is_string($namingContext)) {
            $search = $this->search()
                ->setDn($namingContext)
                ->select($fields);

            if ($sorted) {
                $search->sortBy($sortBy, $sortDirection);
            }

            return $search->get();
        }

        return false;
    }

    /**
     * Creates a new search limited to exchange servers only.
     *
     * @return \Adldap\Query\Builder
     */
    public function search()
    {
        $schema = Schema::get();

        return $this->getManager()
            ->search()
            ->whereEquals($schema->objectCategory(), $schema->objectCategoryExchangeServer());
    }

    /**
     * Returns a list of Storage Groups in Exchange for a given mail server.
     *
     * @param string $exchangeServer The full DN of an Exchange server.  You can use exchange_servers() to find the DN for your server
     *
     * @return bool|array
     */
    public function storageGroups($exchangeServer)
    {
        $schema = Schema::get();

        return $this->getManager()->search()
            ->setDn($exchangeServer)
            ->whereEquals($schema->objectCategory(), $schema->objectCategoryExchangeStorageGroup())
            ->get();
    }

    /**
     * Returns a list of Databases within any given storage group in Exchange for a given mail server.
     *
     * @param string $storageGroup The full DN of an Storage Group.  You can use exchange_storage_groups() to find the DN
     *
     * @return bool|array
     */
    public function storageDatabases($storageGroup)
    {
        $schema = Schema::get();

        return $this->getManager()->search()
            ->setDn($storageGroup)
            ->whereEquals($schema->objectCategory(), $schema->objectCategoryExchangePrivateMdb())
            ->get();
    }

    /**
     * Returns the current configuration naming context of the current domain.
     *
     * @return bool|string
     */
    private function getConfigurationNamingContext()
    {
        $result = $this->getManager()->getRootDse();

        if ($result instanceof Entry) {
            return $result->getAttribute(Schema::get()->configurationNamingContext(), 0);
        }

        return false;
    }
}
