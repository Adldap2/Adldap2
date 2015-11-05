<?php

namespace Adldap\Scopes;

use Adldap\Schemas\ActiveDirectory;
use Adldap\Schemas\Schema;

class Printers extends AbstractScope implements QueryableInterface
{
    /**
     * Finds a printer.
     *
     * @param string $name
     * @param array  $fields
     *
     * @return bool|\Adldap\Models\User
     */
    public function find($name, $fields = [])
    {
        return $this->search()->select($fields)->find($name);
    }

    /**
     * Returns all printers.
     *
     * @param array     $fields
     * @param bool|true $sorted
     * @param string    $sortBy
     * @param string    $sortDirection
     *
     * @return \Doctrine\Common\Collections\ArrayCollection|bool
     */
    public function all($fields = [], $sorted = true, $sortBy = ActiveDirectory::COMMON_NAME, $sortDirection = 'asc')
    {
        $search = $this->search()->select($fields);

        if ($sorted) {
            $search->sortBy($sortBy, $sortDirection);
        }

        return $search->get();
    }

    /**
     * Creates a new search limited to printers only.
     *
     * @return \Adldap\Query\Builder
     */
    public function search()
    {
        $schema = Schema::get();

        return $this->getManager()
            ->search()
            ->whereEquals($schema->objectClass(), $schema->objectClassPrinter());
    }
}
