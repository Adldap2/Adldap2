<?php

namespace Adldap\Classes;

use Adldap\Adldap;

/**
 * The Base Adldap class.
 *
 * Class AdldapBase
 */
abstract class AbstractBase
{
    /**
     * The current Adldap connection via dependency injection.
     *
     * @var Adldap
     */
    protected $adldap;

    /**
     * The current Adldap connection.
     *
     * @var \Adldap\Connections\ConnectionInterface
     */
    protected $connection;

    /**
     * Constructor.
     *
     * @param Adldap $adldap
     */
    public function __construct(Adldap $adldap)
    {
        $this->adldap = $adldap;

        $connection = $adldap->getConnection();

        if ($connection) {
            $this->connection = $connection;
        }
    }

    /**
     * Returns the current Adldap instance.
     *
     * @return Adldap
     */
    public function getAdldap()
    {
        return $this->adldap;
    }
}
