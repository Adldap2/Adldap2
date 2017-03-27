<?php

namespace Adldap\Tests\Models;

use Adldap\Utilities;
use Adldap\Models\User;
use Adldap\Tests\TestCase;

class UserTest extends TestCase
{
    public function test_set_password()
    {
        $connection = $this->newConnectionMock();

        $connection->shouldReceive('isUsingSSL')->once()->andReturn(true);

        $user = new User([], $this->newBuilder($connection));

        $user->setPassword('');

        $expected = [
            [
                'attrib'    => 'unicodepwd',
                'modtype'   => 3,
                'values'    => [Utilities::encodePassword('')],
            ],
        ];

        $this->assertEquals($expected, $user->getModifications());
    }

    /**
     * @expectedException \Adldap\AdldapException
     */
    public function test_set_password_without_ssl_or_tls()
    {
        $connection = $this->newConnectionMock();

        $connection->shouldReceive('isUsingSSL')->once()->andReturn(false);
        $connection->shouldReceive('isUsingTLS')->once()->andReturn(false);

        $user = new User([], $this->newBuilder($connection));

        $user->setPassword('');
    }

    /**
     * @expectedException \Adldap\Models\UserPasswordPolicyException
     */
    public function test_change_password_policy_failure()
    {
        $connection = $this->newConnectionMock();

        $connection->shouldReceive('isUsingSSL')->once()->andReturn(true);
        $connection->shouldReceive('modifyBatch')->once()->andReturn(false);
        $connection->shouldReceive('getExtendedError')->once()->andReturn('error');
        $connection->shouldReceive('getExtendedErrorCode')->once()->andReturn('0000052D');

        $user = new User([], $this->newBuilder($connection));

        $user->changePassword('', '');
    }

    /**
     * @expectedException \Adldap\Models\UserPasswordIncorrectException
     */
    public function test_change_password_wrong_failure()
    {
        $connection = $this->newConnectionMock();

        $connection->shouldReceive('isUsingSSL')->once()->andReturn(true);
        $connection->shouldReceive('modifyBatch')->once()->andReturn(false);
        $connection->shouldReceive('getExtendedError')->once()->andReturn('error');
        $connection->shouldReceive('getExtendedErrorCode')->once()->andReturn('00000056');

        $user = new User([], $this->newBuilder($connection));

        $user->changePassword('', '');
    }

    /**
     * @expectedException \Adldap\AdldapException
     */
    public function test_change_password_without_ssl_or_tls()
    {
        $connection = $this->newConnectionMock();

        $connection->shouldReceive('isUsingSSL')->once()->andReturn(false);
        $connection->shouldReceive('isUsingTLS')->once()->andReturn(false);

        $user = new User([], $this->newBuilder($connection));

        $user->changePassword('', '');
    }
}
