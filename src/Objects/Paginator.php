<?php

namespace Adldap\Objects;

use ArrayIterator;
use Countable;
use IteratorAggregate;

class Paginator implements Countable, IteratorAggregate
{
    /**
     * The complete results array.
     *
     * @var array
     */
    protected $results = [];

    /**
     * The total amount of pages.
     *
     * @var int
     */
    protected $pages;

    /**
     * The amount of entries per page.
     *
     * @var int
     */
    protected $perPage;

    /**
     * The current page number.
     *
     * @var int
     */
    protected $currentPage;

    /**
     * The current entry offset number.
     *
     * @var int
     */
    protected $currentOffset;

    /**
     * Constructor.
     *
     * @param array $results
     * @param int   $perPage
     * @param int   $currentPage
     * @param int   $pages
     */
    public function __construct(array $results = [], $perPage = 50, $currentPage = 0, $pages = 0)
    {
        $this->setResults($results);

        $this->setPerPage($perPage);

        $this->setCurrentPage($currentPage);

        $this->setPages($pages);

        // Set the offset for slicing the entries array
        $this->setCurrentOffset(($this->getCurrentPage() * $this->getPerPage()));
    }

    /**
     * Get an iterator for the entries.
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        // Slice the the entries
        $entries = array_slice($this->getResults(), $this->getCurrentOffset(), $this->getPerPage(), true);

        // Return the array iterator
        return new ArrayIterator($entries);
    }

    /**
     * Returns the complete results array.
     *
     * @return array
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * Returns the total amount of pages
     * in a paginated result.
     *
     * @return int
     */
    public function getPages()
    {
        return $this->pages;
    }

    /**
     * Returns the total amount of entries
     * allowed per page.
     *
     * @return int
     */
    public function getPerPage()
    {
        return $this->perPage;
    }

    /**
     * Returns the current page number.
     *
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * Returns the current offset number.
     *
     * @return int
     */
    public function getCurrentOffset()
    {
        return $this->currentOffset;
    }

    /**
     * Returns the total amount of results.
     *
     * @return int
     */
    public function count()
    {
        return count($this->results);
    }

    /**
     * Sets the results array property.
     *
     * @param array $results
     */
    private function setResults(array $results)
    {
        $this->results = $results;
    }

    /**
     * Sets the total number of pages.
     *
     * @param int $pages
     */
    private function setPages($pages = 0)
    {
        $this->pages = (int) $pages;
    }

    /**
     * Sets the number of entries per page.
     *
     * @param int $perPage
     */
    private function setPerPage($perPage = 50)
    {
        $this->perPage = (int) $perPage;
    }

    /**
     * Sets the current page number.
     *
     * @param int $currentPage
     */
    private function setCurrentPage($currentPage = 0)
    {
        $this->currentPage = (int) $currentPage;
    }

    /**
     * Sets the current offset number.
     *
     * @param int $offset
     */
    private function setCurrentOffset($offset = 0)
    {
        $this->currentOffset = (int) $offset;
    }
}
