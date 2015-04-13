<?php

namespace adLDAP\classes;

use adLDAP\adLDAP;

/**
 * The base adLDAP class
 *
 * Class adLDAPBase
 * @package adLDAP\classes
 */
class adLDAPBase
{
    /**
     * The current adLDAP connection via dependency injection
     *
     * @var adLDAP
     */
    protected $adldap;

    /**
     * The current adLDAP connection
     *
     * @var \adLDAP\Interfaces\ConnectionInterface
     */
    protected $connection;

    /**
     * Constructor.
     *
     * @param adLDAP $adldap
     */
    public function __construct(adLDAP $adldap)
    {
        $this->adldap = $adldap;

        $this->connection = $adldap->getLdapConnection();
    }
}