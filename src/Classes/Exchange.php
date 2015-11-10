<?php

namespace Adldap\Classes;

use Adldap\Models\Entry;
use Adldap\Schemas\ActiveDirectory;

class Exchange extends AbstractBase implements QueryableInterface
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
                ->findBy(ActiveDirectory::COMMON_NAME, $name, $fields);
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
     * @return array|bool
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
        return $this->getAdldap()
            ->search()
            ->whereEquals(ActiveDirectory::OBJECT_CATEGORY, ActiveDirectory::OBJECT_CATEGORY_EXCHANGE_SERVER);
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
        return $this->getAdldap()->search()
            ->setDn($exchangeServer)
            ->where(ActiveDirectory::OBJECT_CATEGORY, '=', ActiveDirectory::OBJECT_CATEGORY_EXCHANGE_STORAGE_GROUP)
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
        return $this->getAdldap()->search()
            ->setDn($storageGroup)
            ->where(ActiveDirectory::OBJECT_CATEGORY, '=', ActiveDirectory::OBJECT_CATEGORY_EXCHANGE_PRIVATE_MDB)
            ->get();
    }

    /**
     * Returns the current configuration naming context of the current domain.
     *
     * @return bool|string
     */
    private function getConfigurationNamingContext()
    {
        $result = $this->getAdldap()->getRootDse();

        if ($result instanceof Entry) {
            return $result->getAttribute(ActiveDirectory::CONFIGURATION_NAMING_CONTEXT, 0);
        }

        return false;
    }
}
