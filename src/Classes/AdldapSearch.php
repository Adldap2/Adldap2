<?php

namespace Adldap\Classes;

use Adldap\Objects\LdapEntry;
use Adldap\Objects\LdapOperator;
use Adldap\Objects\Paginator;
use Adldap\Query\Builder;
use Adldap\Adldap;

/**
 * Class AdldapSearch.
 */
class AdldapSearch extends AdldapBase
{
    /**
     * Stores the current query builder instance.
     *
     * @var Builder
     */
    protected $query;

    /**
     * Stores the distinguished name to search on.
     *
     * @var string
     */
    protected $dn = '';

    /**
     * Stores the bool to determine whether or not
     * to search LDAP recursively.
     *
     * @var bool
     */
    protected $recursive = true;

    /**
     * Stores the field to sort search results by.
     *
     * @var string
     */
    protected $sortByField = '';

    /**
     * Stores the direction to sort the search results by.
     *
     * @var string
     */
    protected $sortByDirection = 'DESC';

    /**
     * Constructor.
     *
     * Sets a new query builder instance.
     */
    public function __construct(Adldap $adldap)
    {
        parent::__construct($adldap);

        $this->setQuery(new Builder($this->connection));
    }

    /**
     * Performs the specified query on the current LDAP connection.
     *
     * @param string $query
     *
     * @return bool|array
     */
    public function query($query)
    {
        // If the query is empty, we'll return false
        if ($query === null || empty($query)) {
            return false;
        }
        /*
         * If the search is recursive, we'll run a search,
         * if not, we'll run a listing.
         */
        if ($this->recursive) {
            $results = $this->connection->search($this->getDn(), $query, $this->getSelects());
        } else {
            $results = $this->connection->listing($this->getDn(), $query, $this->getSelects());
        }

        if ($results) {
            return $this->processResults($results);
        }

        return false;
    }

    /**
     * Performs the current query on the current LDAP connection.
     *
     * @return array|bool
     */
    public function get()
    {
        return $this->query($this->getQuery());
    }

    /**
     * Performs a global 'all' search query on the
     * current connection.
     *
     * @return array|bool
     */
    public function all()
    {
        $this->query->where('objectClass', LdapOperator::$wildcard);

        return $this->get();
    }

    /**
     * Paginates the current LDAP query.
     *
     * @param int  $perPage
     * @param int  $currentPage
     * @param bool $isCritical
     *
     * @return bool
     */
    public function paginate($perPage = 50, $currentPage = 0, $isCritical = true)
    {
        // Stores all LDAP entries in a page array
        $pages = [];

        $cookie = '';

        do {
            $this->connection->controlPagedResult($perPage, $isCritical, $cookie);

            $results = $this->connection->search($this->adldap->getBaseDn(), $this->getQuery(), $this->getSelects());

            if ($results) {
                $this->connection->controlPagedResultResponse($results, $cookie);

                $pages[] = $results;
            }
        } while ($cookie !== null && ! empty($cookie));

        if (count($pages) > 0) {
            return $this->processPaginatedResults($pages, $perPage, $currentPage);
        }

        return false;
    }

    /**
     * Returns the first entry in a search result.
     *
     * @return array|bool
     */
    public function first()
    {
        $results = $this->get();

        if (is_array($results) && array_key_exists(0, $results)) {
            return $results[0];
        }

        return $results;
    }

    /**
     * Adds the inserted fields to query on the current LDAP connection.
     *
     * @param array $fields
     *
     * @return $this
     */
    public function select($fields = [])
    {
        $this->query->select($fields);

        return $this;
    }

    /**
     * Adds a where clause to the current query.
     *
     * @param $field
     * @param null $operator
     * @param null $value
     *
     * @return $this
     */
    public function where($field, $operator = null, $value = null)
    {
        $this->query->where($field, $operator, $value);

        return $this;
    }

    /**
     * Adds an orWhere clause to the current query.
     *
     * @param string $field
     * @param null   $operator
     * @param null   $value
     *
     * @return $this
     */
    public function orWhere($field, $operator = null, $value = null)
    {
        $this->query->orWhere($field, $operator, $value);

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
        return $this->query->hasSelects();
    }

    /**
     * Returns the current selected fields to retrieve.
     *
     * @return array
     */
    public function getSelects()
    {
        return $this->query->getSelects();
    }

    /**
     * Returns the wheres on the current search object.
     *
     * @return array
     */
    public function getWheres()
    {
        return $this->query->getWheres();
    }

    /**
     * Returns the or wheres on the current search object.
     *
     * @return array
     */
    public function getOrWheres()
    {
        return $this->query->getOrWheres();
    }

    /**
     * Returns the current LDAP query string.
     *
     * @return string
     */
    public function getQuery()
    {
        return $this->query->get();
    }

    /**
     * Sorts the LDAP search results by the specified field
     * and direction.
     *
     * @param $field
     * @param string $direction
     *
     * @return $this
     */
    public function sortBy($field, $direction = 'desc')
    {
        $this->sortByField = $field;

        if (strtolower($direction) === 'asc') {
            $this->sortByDirection = SORT_ASC;
        } else {
            $this->sortByDirection = SORT_DESC;
        }

        return $this;
    }

    /**
     * Sets the complete distinguished name to search on.
     *
     * @param string $dn
     *
     * @return $this
     */
    public function setDn($dn)
    {
        $this->dn = (string) $dn;

        return $this;
    }

    /**
     * Returns the current distinguished name.
     *
     * This will return the domains base DN if a search
     * DN is not set.
     *
     * @return string
     */
    public function getDn()
    {
        if (empty($this->dn)) {
            return $this->adldap->getBaseDn();
        }

        return $this->dn;
    }

    /**
     * Sets the recursive property to tell the search
     * whether or not to search recursively.
     *
     * @param bool $recursive
     *
     * @return $this
     */
    public function recursive($recursive = true)
    {
        $this->recursive = true;

        if ($recursive === false) {
            $this->recursive = false;
        }

        return $this;
    }

    /**
     * Sets the query property.
     *
     * @param Builder $query
     */
    private function setQuery(Builder $query)
    {
        $this->query = $query;
    }

    /**
     * Processes LDAP search results into a nice array.
     *
     * @param resource $results
     *
     * @return array
     */
    private function processResults($results)
    {
        $entries = $this->connection->getEntries($results);

        $objects = [];

        if (array_key_exists('count', $entries)) {
            for ($i = 0; $i < $entries['count']; $i++) {
                $entry = new LdapEntry($entries[$i], $this->connection);

                $objects[] = $entry->getAttributes();
            }

            if (! empty($this->sortByField)) {
                return $this->processSortBy($objects);
            }
        }

        return $objects;
    }

    /**
     * Processes paginated LDAP results.
     *
     * @param array $pages
     * @param int   $perPage
     * @param int   $currentPage
     *
     * @return array|bool
     */
    private function processPaginatedResults($pages, $perPage = 50, $currentPage = 0)
    {
        // Make sure we have at least one page of results
        if (count($pages) > 0) {
            $objects = [];

            // Go through each page
            foreach ($pages as $results) {
                // Get the entries for each page
                $entries = $this->connection->getEntries($results);

                /*
                 * If we've retrieved entries, we'll go through
                 * each and construct the entry attributes, and
                 * put them all inside the objects array
                 */
                if (is_array($entries) && array_key_exists('count', $entries)) {
                    for ($i = 0; $i < $entries['count']; $i++) {
                        $entry = new LdapEntry($entries[$i], $this->connection);

                        $objects[] = $entry->getAttributes();
                    }
                }
            }

            /*
             * If we're sorting, we'll process all of
             * our results so it's sorted correctly
             */
            if (! empty($this->sortByField)) {
                $objects = $this->processSortBy($objects);
            }

            // Return a new Paginator instance
            return new Paginator($objects, $perPage, $currentPage, count($pages));
        }

        // Looks like we don't have any results, return false
        return false;
    }

    /**
     * Processes the array of specified object results
     * and sorts them by the field and direction search
     * property.
     *
     * @param $objects
     * @param array
     */
    private function processSortBy($objects)
    {
        if (count($objects) > 0) {
            foreach ($objects as $key => $row) {
                if (array_key_exists($this->sortByField, $row)) {
                    $sort[$key] = $row[$this->sortByField];
                }
            }

            array_multisort($sort, $this->sortByDirection, $objects);
        }

        return $objects;
    }
}
