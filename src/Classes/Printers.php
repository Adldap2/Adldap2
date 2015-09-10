<?php

namespace Adldap\Classes;

use Adldap\Schemas\ActiveDirectory;

class Printers extends AbstractBase implements QueryableInterface
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
     *
     * @return array|bool
     */
    public function all($fields = [], $sorted = true, $sortBy = 'cn')
    {
        $search = $this->search()->select($fields);

        if ($sorted) {
            $search->sortBy($sortBy);
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
        return $this->getAdldap()
            ->search()
            ->whereEquals(ActiveDirectory::OBJECT_CLASS, ActiveDirectory::OBJECT_CLASS_PRINTER);
    }
}
