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

        $connection->shouldReceive('isSupported')->andReturn(false);

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
        $this->setExpectedException('adLDAP\Exceptions\adLDAPException');

        $config = array(
            'domain_controllers' => 'test'
        );

        new adLDAP($config);
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