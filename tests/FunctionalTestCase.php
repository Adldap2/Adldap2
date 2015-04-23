<?php

namespace Adldap\Tests;

use Mockery;

abstract class FunctionalTestCase extends \PHPUnit_Framework_TestCase
{
    public $configStub = [
        'account_suffix' => 'Account Suffix',
        'base_dn' => 'Base DN',
        'domain_controllers' => ['dc1', 'dc2'],
        'admin_username' => 'Admin Username',
        'admin_password' => 'Admin Password',
        'real_primarygroup' => 'Primary Group',
        'use_ssl' => true,
        'use_tls' => true,
        'sso' => true,
        'recursive_groups' => true,
        'follow_referrals' => true,
        'ad_port' => 500,
    ];

    /**
     * Returns a mocked version of the specified class
     *
     * @param $class
     * @return Mockery\MockInterface
     */
    protected function mock($class)
    {
        return Mockery::mock($class);
    }

    /**
     * Returns a mocked connection
     *
     * @return Mockery\MockInterface
     */
    protected function newConnectionMock()
    {
        return $this->mock('Adldap\Interfaces\ConnectionInterface');
    }

    public function setUp()
    {
        // Set constants for testing without LDAP support
        if( ! defined('LDAP_OPT_PROTOCOL_VERSION')) define('LDAP_OPT_PROTOCOL_VERSION', 3);

        if( ! defined('LDAP_OPT_REFERRALS')) define('LDAP_OPT_REFERRALS', 0);

        if( ! array_key_exists('REMOTE_USER', $_SERVER)) $_SERVER['REMOTE_USER'] = 'true';

        if( ! array_key_exists('KRB5CCNAME', $_SERVER)) $_SERVER['KRB5CCNAME'] = 'true';

        parent::setUp();
    }

    public function tearDown()
    {
        Mockery::close();
    }
}