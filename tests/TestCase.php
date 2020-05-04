<?php

namespace Adldap\Tests;

use Mockery;
use Adldap\Utilities;
use Adldap\Models\User;
use Adldap\Query\Builder;
use Adldap\Query\Grammar;
use Adldap\Connections\ConnectionInterface;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class TestCase extends MockeryTestCase
{
    /*
     * Set up the test environment.
     */
    protected function setUp()
    {
        if (!defined('LDAP_CONTROL_PAGEDRESULTS')) {
            define('LDAP_CONTROL_PAGEDRESULTS', '1.2.840.113556.1.4.319');
        }

        // Set constants for testing without LDAP support
        if (!defined('LDAP_OPT_PROTOCOL_VERSION')) {
            define('LDAP_OPT_PROTOCOL_VERSION', 3);
        }

        if (!defined('LDAP_OPT_REFERRALS')) {
            define('LDAP_OPT_REFERRALS', 0);
        }

        if (!array_key_exists('REMOTE_USER', $_SERVER)) {
            $_SERVER['REMOTE_USER'] = 'true';
        }

        if (!array_key_exists('KRB5CCNAME', $_SERVER)) {
            $_SERVER['KRB5CCNAME'] = 'true';
        }
    }

    protected function tearDown()
    {
        User::usePasswordStrategy(function ($password) {
            return Utilities::encodePassword($password);
        });

        parent::tearDown();
    }

    /**
     * Mocks a the specified class.
     *
     * @param mixed $class
     *
     * @return Mockery\MockInterface
     */
    protected function mock($class)
    {
        return Mockery::mock($class);
    }

    /**
     * Returns a new Builder instance.
     *
     * @param null $connection
     *
     * @return Builder
     */
    protected function newBuilder($connection = null)
    {
        if (is_null($connection)) {
            $connection = $this->newConnectionMock();
        }

        return new Builder($connection, new Grammar());
    }

    /**
     * Returns a mocked builder instance.
     *
     * @param null $connection
     *
     * @return Mockery\MockInterface
     */
    protected function newBuilderMock($connection = null)
    {
        return $this->mock($this->newBuilder($connection));
    }

    /**
     * Returns a mocked connection instance.
     *
     * @return Mockery\MockInterface
     */
    protected function newConnectionMock()
    {
        return $this->mock(ConnectionInterface::class);
    }
}
