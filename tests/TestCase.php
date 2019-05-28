<?php

namespace Adldap\Tests;

use Mockery;
use Adldap\Query\Builder;
use Adldap\Query\Grammar;
use Adldap\Connections\ConnectionInterface;
use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    /*
     * Set up the test environment.
     */
    public function setUp()
    {
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
