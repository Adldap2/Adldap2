<?php

namespace Adldap\Objects;

use ArrayIterator;
use IteratorAggregate;

/**
 * Allows easy pagination for a paginated LDAP result.
 *
 * Class Paginator
 */
class Paginator extends AbstractObject implements IteratorAggregate
{
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
     * @param array $entries
     * @param int   $perPage
     * @param int   $currentPage
     * @param int   $pages
     */
    public function __construct(array $entries = [], $perPage = 50, $currentPage = 0, $pages = 0)
    {
        $this->setAttributes($entries);

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
        $entries = array_slice($this->getAttributes(), $this->getCurrentOffset(), $this->getPerPage(), true);

        // Return the array iterator
        return new ArrayIterator($entries);
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
     * Returns the total amount of entries.
     *
     * @return int
     */
    public function count()
    {
        return $this->countAttributes();
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
