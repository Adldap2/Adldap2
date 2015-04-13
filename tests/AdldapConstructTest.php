<?php

namespace adLDAP\Tests;

use adLDAP\adLDAP;

class AdldapConstructTest extends FunctionalTestCase
{
    /**
     * This tests that the connection function isSupported
     * returns false, and an exception is thrown when it
     * has been called.
     */
    public function testAdldapConstructLdapNotSupportedFailure()
    {
        $this->setExpectedException('adLDAP\Exceptions\adLDAPException');

        $connection = $this->newConnectionMock();

        $connection->shouldReceive('isSupported')->once()->andReturn(false);

        new adLDAP(array(), $connection);
    }

    /**
     * This tests that the first configuration parameter
     * must be an array, and will fail constructing with
     * another variable type.
     */
    public function testAdldapConstructConfigNotArrayFailure()
    {
        try
        {
            new adLDAP('test');

            $passes = false;
        } catch(\Exception $e)
        {
            $passes = true;
        }

        $this->assertTrue($passes);
    }

    /**
     * This tests that a recoverable exception was thrown
     * when an invalid type hinted connection is passed into
     * the connection parameter in the adLDAP constructor.
     */
    public function testAdlapConstructInvalidConnectionTypeHint()
    {
        $connection = 'test';

        try
        {
            new adLDAP(array(), $connection);

            $passes = false;
        } catch(\Exception $e)
        {
            $passes = true;
        }

        $this->assertTrue($passes);
    }

    /**
     * This tests that the domain_controllers key must
     * be an array and an exception is thrown when it
     * is set to another type.
     */
    public function testAdldapConstructDomainControllerFailure()
    {
        $config = array(
            'domain_controllers' => 'test'
        );

        try
        {
            new adLDAP($config);

            $passes = false;
        } catch (\Exception $e)
        {
            $passes = true;
        }

        $this->assertTrue($passes);
    }

    /**
     * This test demonstrates that all attributes are set
     * properly on construct.
     */
    public function testAdldapConstructConfig()
    {
        $connection = $this->newConnectionMock();

        $config = array(
            'account_suffix' => 'Account Suffix',
            'base_dn' => 'Base DN',
            'domain_controllers' => array('dc1', 'dc2'),
            'admin_username' => 'Admin Username',
            'admin_password' => 'Admin Password',
            'real_primarygroup' => 'Primary Group',
            'use_ssl' => true,
            'use_tls' => true,
            'sso' => true,
            'recursive_groups' => true,
            'follow_referrals' => true,
            'ad_port' => 500,
        );

        $connection
            ->shouldReceive('isSupported')->andReturn(true)
            ->shouldReceive('isSaslSupported')->andReturn(true)
            ->shouldReceive('useSSO')->andReturn(true)
            ->shouldReceive('useSSL')->andReturn(true)
            ->shouldReceive('useTLS')->andReturn(true)
            ->shouldReceive('startTLS')->andReturn(true)
            ->shouldReceive('isUsingSSL')->andReturn(true)
            ->shouldReceive('isUsingTLS')->andReturn(true)
            ->shouldReceive('isUsingSSO')->andReturn(true)
            ->shouldReceive('connect')->andReturn(true)
            ->shouldReceive('setOption')->twice()
            ->shouldReceive('bind')->andReturn('resource')
            ->shouldReceive('close')->andReturn(true);

        $ad = new adLDAP($config, $connection);

        $this->assertInstanceOf('adLDAP\Interfaces\ConnectionInterface', $ad->getLdapConnection());

        $this->assertEquals(500, $ad->getPort());
        $this->assertEquals(array('dc1', 'dc2'), $ad->getDomainControllers());
        $this->assertEquals('Base DN', $ad->getBaseDn());
        $this->assertEquals('Account Suffix', $ad->getAccountSuffix());

        $this->assertTrue($ad->getRecursiveGroups());
        $this->assertTrue($ad->getUseSSL());
        $this->assertTrue($ad->getUseTLS());
        $this->assertTrue($ad->getUseSSO());
    }

    /**
     * This tests that when sso configuration property
     * is true, the method getUseSSO must return true.
     */
    public function testAdldapConstructUseSSO()
    {
        $connection = $this->newConnectionMock();

        $config = array(
            'sso' => true,
            'domain_controllers' => array('domain'),
            'ad_port' => '100'
        );

        /*
         * This demonstrates the entire walk-through of each
         * method on the connection when SSO is enabled
         */
        $connection
            ->shouldReceive('isSupported')->andReturn(true)
            ->shouldReceive('isSaslSupported')->andReturn(true)
            ->shouldReceive('useSSO')->andReturn(true)
            ->shouldReceive('isUsingSSL')->andReturn(false)
            ->shouldReceive('isUsingTLS')->andReturn(false)
            ->shouldReceive('isUsingSSO')->andReturn(true)
            ->shouldReceive('connect')->andReturn(true)
            ->shouldReceive('setOption')->twice()
            ->shouldReceive('bind')->andReturn('resource')
            ->shouldReceive('close')->andReturn(true);

        $ad = new adLDAP($config, $connection);

        $this->assertTrue($ad->getUseSSO());
    }
}