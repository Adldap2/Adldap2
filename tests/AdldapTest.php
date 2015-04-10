<?php

namespace adLDAP\Tests;

use adLDAP\adLDAP;

class AdldapTest extends FunctionalTestCase
{
    protected function newConnectionMock()
    {
        return $this->mock('adLDAP\Connections\Ldap');
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
        $this->setExpectedException('adLDAP\adLDAPException');

        $config = array(
            'domain_controllers' => 'test'
        );

        new adLDAP($config);
    }

    /**
     * This tests that the connection function isSupported
     * returns false, and an exception is thrown when it
     * has been checked.
     */
    public function testAdldapConstructLdapNotSupportedFailure()
    {
        $this->setExpectedException('adLDAP\adLDAPException');

        $connection = $this->newConnectionMock();

        $connection->shouldReceive('isSupported')->andReturn(false);

        new adLDAP(array(), $connection);
    }

    public function testAdldapConstructUseSSO()
    {
        $connection = $this->mock('adLDAP\Connections\Ldap');

        $config = array(
            'sso' => true,
        );

        $connection
            ->shouldReceive('isSupported')->andReturn(true)
            ->shouldReceive('isSaslSupported')->andReturn(true)
            ->shouldReceive('useSSO')->andReturn(true)
            ->shouldReceive('connect')->andReturn(true)
            ->shouldReceive('setOption')->twice()
            ->shouldReceive('bind')->andReturn('resource')
            ->shouldReceive('isUsingSSO')->andReturn(true);

        $ad = new adLDAP($config, $connection);

        $this->assertTrue($ad->getUseSSO());
    }

    /**
     * This tests that the first configuration parameter
     * must be an array, and will fail constructing with
     * another variable type.
     */
    public function testAdldapConfigNotArrayConstructFailure()
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
}