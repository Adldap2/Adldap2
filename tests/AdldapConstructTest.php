<?php

namespace Adldap\Tests;

use Adldap\Adldap;
use Adldap\Connections\Configuration;

class AdldapConstructTest extends FunctionalTestCase
{
    /**
     * This tests that the first configuration parameter
     * must be a Configuration instance, and will fail constructing with
     * another variable type.
     */
    public function testAdldapConstructNotConfigurationInstanceFailure()
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
            new Adldap(new Configuration(), $connection);

            $passes = false;
        } catch(\Exception $e)
        {
            $passes = true;
        }

        $this->assertTrue($passes);
    }

    /**
     * This test demonstrates that all
     * attributes are set properly on construct.
     */
    public function testAdldapConstructConfig()
    {
        $connection = $this->newConnectionMock();

        $connection->shouldReceive('isSupported')->andReturn(true);
        $connection->shouldReceive('isSaslSupported')->andReturn(true);
        $connection->shouldReceive('useSSO')->andReturn(true);
        $connection->shouldReceive('useSSL')->andReturn(true);
        $connection->shouldReceive('useTLS')->andReturn(true);
        $connection->shouldReceive('startTLS')->andReturn(true);
        $connection->shouldReceive('isUsingSSL')->andReturn(true);
        $connection->shouldReceive('isUsingTLS')->andReturn(true);
        $connection->shouldReceive('isUsingSSO')->andReturn(true);
        $connection->shouldReceive('connect')->once()->andReturn(true);
        $connection->shouldReceive('setOption')->twice()->andReturn(true);
        $connection->shouldReceive('bind')->andReturn('resource');
        $connection->shouldReceive('isBound')->andReturn(true);
        $connection->shouldReceive('close')->andReturn(true);

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
     * This tests that when auto-connect is false,
     * the connect method is not called on the current
     * connection until manually called.
     */
    public function testAdldapConstructNoAutoConnect()
    {
        $connection = $this->newConnectionMock();

        $differentConnection = $this->newConnectionMock();

        $ad = new Adldap(new Configuration(), $connection, false);

        $differentConnection->shouldReceive('close')->once()->andReturn(true);

        $ad->setConnection($differentConnection);
    }
}