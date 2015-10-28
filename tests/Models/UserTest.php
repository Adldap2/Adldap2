<?php

namespace Adldap\Tests\Models;

use Adldap\Utilities;
use Adldap\Models\User;
use Adldap\Tests\UnitTestCase;

class UserTest extends UnitTestCase
{
    public function testSetPassword()
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
            ]
        ];

        $this->assertEquals($expected, $user->getModifications());
    }

    public function testSetPasswordWithoutSSLOrTLS()
    {
        $connection = $this->newConnectionMock();

        $connection->shouldReceive('isUsingSSL')->once()->andReturn(false);
        $connection->shouldReceive('isUsingTLS')->once()->andReturn(false);

        $user = new User([], $this->newBuilder($connection));

        $this->setExpectedException('Adldap\Exceptions\AdldapException');

        $user->setPassword('');
    }

    public function testChangePasswordPolicyFailure()
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

    public function testChangePasswordWrongFailure()
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

    public function testChangePasswordWithoutSSLOrTLS()
    {
        $connection = $this->newConnectionMock();

        $connection->shouldReceive('isUsingSSL')->once()->andReturn(false);
        $connection->shouldReceive('isUsingTLS')->once()->andReturn(false);

        $user = new User([], $this->newBuilder($connection));

        $this->setExpectedException('Adldap\Exceptions\AdldapException');

        $user->changePassword('', '');
    }
}
