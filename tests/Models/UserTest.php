<?php

namespace Adldap\Tests\Models;

use Adldap\Models\User;
use Adldap\Tests\TestCase;
use Adldap\Utilities;

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

    public function test_set_password_without_ssl_or_tls()
    {
        $connection = $this->newConnectionMock();

        $connection->shouldReceive('isUsingSSL')->once()->andReturn(false);
        $connection->shouldReceive('isUsingTLS')->once()->andReturn(false);

        $user = new User([], $this->newBuilder($connection));

        $this->setExpectedException('Adldap\Exceptions\AdldapException');

        $user->setPassword('');
    }

    public function test_change_password_policy_failure()
    {
        $connection = $this->newConnectionMock();

        $code = '0000052D';

        $connection->shouldReceive('isUsingSSL')->once()->andReturn(true);
        $connection->shouldReceive('modifyBatch')->once()->andReturn(false);
        $connection->shouldReceive('getExtendedError')->once()->andReturn('error');
        $connection->shouldReceive('getExtendedErrorCode')->once()->andReturn($code);

        $user = new User([], $this->newBuilder($connection));

        $this->setExpectedException('Adldap\Exceptions\PasswordPolicyException');

        $user->changePassword('', '');
    }

    public function test_change_password_wrong_failure()
    {
        $connection = $this->newConnectionMock();

        $code = '00000056';

        $connection->shouldReceive('isUsingSSL')->once()->andReturn(true);
        $connection->shouldReceive('modifyBatch')->once()->andReturn(false);
        $connection->shouldReceive('getExtendedError')->once()->andReturn('error');
        $connection->shouldReceive('getExtendedErrorCode')->once()->andReturn($code);

        $user = new User([], $this->newBuilder($connection));

        $this->setExpectedException('Adldap\Exceptions\WrongPasswordException');

        $user->changePassword('', '');
    }

    public function test_change_password_without_ssl_or_tls()
    {
        $connection = $this->newConnectionMock();

        $connection->shouldReceive('isUsingSSL')->once()->andReturn(false);
        $connection->shouldReceive('isUsingTLS')->once()->andReturn(false);

        $user = new User([], $this->newBuilder($connection));

        $this->setExpectedException('Adldap\Exceptions\AdldapException');

        $user->changePassword('', '');
    }
}
