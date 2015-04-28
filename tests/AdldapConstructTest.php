<?php

namespace Adldap\Tests;

use Adldap\Adldap;

class AdldapConstructTest extends FunctionalTestCase
{
    /**
     * This tests that the first configuration parameter
     * must be an array, and will fail constructing with
     * another variable type.
     */
    public function testAdldapConstructConfigNotArrayFailure()
    {
        try
        {
            new Adldap('test');

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
     * the connection parameter in the Adldap constructor.
     */
    public function testAdlapConstructInvalidConnectionTypeHint()
    {
        $connection = 'test';

        try
        {
            new Adldap([], $connection);

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
        $config = [
            'domain_controllers' => 'test'
        ];

        try
        {
            new Adldap($config);

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

        $ad = new Adldap($this->configStub, $connection);

        $this->assertInstanceOf('Adldap\Interfaces\ConnectionInterface', $ad->getLdapConnection());

        $this->assertEquals(500, $ad->getPort());
        $this->assertEquals(['dc1', 'dc2'], $ad->getDomainControllers());
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

        $config = [
            'sso' => true,
            'domain_controllers' => ['domain'],
            'ad_port' => '100',
            'base_dn' => 'dc=com',
        ];

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

        $ad = new Adldap($config, $connection);

        $this->assertTrue($ad->getUseSSO());
    }

    /**
     * This tests that when auto-connect is false,
     * the connect method is not called on the current
     * connection until manually called.
     */
    public function testAdldapConstructNoAutoConnect()
    {
        $connection = $this->newConnectionMock();

        $differentConnection = $this->newConnectionMock();

        $ad = new Adldap([], $connection, false);

        $differentConnection->shouldReceive('close')->once()->andReturn(true);

        $ad->setLdapConnection($differentConnection);
    }
}