<?php

namespace adLDAP\Tests;

use Mockery;

// Set constants for testing without LDAP support
if( ! defined('LDAP_OPT_PROTOCOL_VERSION')) define('LDAP_OPT_PROTOCOL_VERSION', 3);

if( ! defined('LDAP_OPT_REFERRALS')) define('LDAP_OPT_REFERRALS', 0);

abstract class FunctionalTestCase extends \PHPUnit_Framework_TestCase
{
    protected function mock($class)
    {
        return Mockery::mock($class);
    }
}