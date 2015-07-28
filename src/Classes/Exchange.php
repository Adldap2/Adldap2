<?php

namespace Adldap\Classes;

use Adldap\Models\Entry;
use Adldap\Schemas\ActiveDirectory;

class Exchange extends AbstractQueryable
{
    /**
     * The exchange servers object category.
     *
     * @var string
     */
    public $serverObjectCategory = 'msExchExchangeServer';

    /**
     * The exchange servers storage group object category.
     *
     * @var string
     */
    public $storageGroupObjectCategory = 'msExchStorageGroup';

    /**
     * Returns all exchange servers.
     *
     * @param array  $fields
     * @param bool   $sorted
     * @param string $sortBy
     * @param string $sortByDirection
     *
     * @return array|bool
     */
    public function all($fields = [], $sorted = true, $sortBy = 'cn', $sortByDirection = 'asc')
    {
        $namingContext = $this->getConfigurationNamingContext();

        if ($namingContext) {
            $search = $this->adldap->search()
                ->setDn($namingContext)
                ->select($fields)
                ->where(ActiveDirectory::OBJECT_CATEGORY, '=', $this->serverObjectCategory);

            if ($sorted) {
                $search->sortBy($sortBy, $sortByDirection);
            }

            return $search->get();
        }

        return false;
    }

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

        if ($namingContext) {
            return $this->adldap->search()
                ->setDn($namingContext)
                ->select($fields)
                ->where(ActiveDirectory::OBJECT_CATEGORY, '=', $this->serverObjectCategory)
                ->where(ActiveDirectory::ANR, '=', $name)
                ->first();
        }

        return false;
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
        return $this->adldap->search()
            ->setDn($exchangeServer)
            ->where(ActiveDirectory::OBJECT_CATEGORY, '=', $this->storageGroupObjectCategory)
            ->get();
    }

    /**
     * Returns a list of Databases within any given storage group in Exchange for a given mail server.
     *
     * @param string $storageGroup The full DN of an Storage Group.  You can use exchange_storage_groups() to find the DN
     *
     * @return array|bool
     */
    public function storageDatabases($storageGroup)
    {
        return $this->adldap->search()
            ->setDn($storageGroup)
            ->where(ActiveDirectory::OBJECT_CATEGORY, '=', ActiveDirectory::MS_EXCHANGE_PRIVATE_MDB)
            ->get();
    }

    /**
     * Returns the current configuration naming context of the current domain.
     *
     * @return string|bool
     */
    private function getConfigurationNamingContext()
    {
        $result = $this->adldap->getRootDse();

        if($result instanceof Entry) {
            return $result->getAttribute(ActiveDirectory::CONFIGURATION_NAMING_CONTEXT, 0);
        }

        return false;
    }
}
