<?php

namespace Adldap\Classes;

use Adldap\Adldap;

/**
 * The Base Adldap class
 *
 * Class AdldapBase
 * @package Adldap\Classes
 */
class AdldapBase
{
    /**
     * The current Adldap connection via dependency injection
     *
     * @var Adldap
     */
    protected $adldap;

    /**
     * The current Adldap connection
     *
     * @var \Adldap\Interfaces\ConnectionInterface
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

        $connection = $adldap->getLdapConnection();

        if($connection) $this->connection = $connection;
    }
}
