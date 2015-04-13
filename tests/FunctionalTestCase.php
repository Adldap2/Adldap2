<?php

namespace adLDAP\Tests;

use Mockery;

abstract class FunctionalTestCase extends \PHPUnit_Framework_TestCase
{
    protected function mock($class)
    {
        return Mockery::mock($class);
    }

    protected function newConnectionMock()
    {
        return $this->mock('adLDAP\Interfaces\ConnectionInterface');
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
}