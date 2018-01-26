<?php

namespace Adldap\Tests\Models;

use Adldap\Utilities;
use Adldap\Models\User;
use Adldap\Tests\TestCase;

class UserTest extends TestCase
{
    protected function newUserModel(array $attributes = [], $builder = null)
    {
        $builder = $builder ?: $this->newBuilder();

        return new User($attributes, $builder);
    }


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

    public function test_set_thumbnail_photo_encodes_images()
    {
        $png = file_get_contents(__DIR__.'/../stubs/placeholder.png');

        $model = $this->newUserModel();

        $model->setThumbnail($png);

        $this->assertEquals(base64_encode($png), $model->getThumbnail());
    }

    public function test_set_jpeg_photo_encodes_images()
    {
        $jpeg = file_get_contents(__DIR__.'/../stubs/placeholder.jpg');

        $model = $this->newUserModel();

        $model->setJpegPhoto($jpeg);

        $this->assertEquals(base64_encode($jpeg), $model->getJpegPhoto());
    }

    public function test_set_user_workstations_accepts_string_or_array()
    {
        $model = $this->newUserModel();

        $model->setUserWorkstations(['ONE','TWO','THREE']);

        $this->assertEquals('ONE,TWO,THREE', $model->getFirstAttribute('userworkstations'));

        $model->setUserWorkstations('ONE,TWO,THREE');

        $this->assertEquals('ONE,TWO,THREE', $model->getFirstAttribute('userworkstations'));
    }

    public function test_get_user_workstations()
    {
        $model = $this->newUserModel([
            'userworkstations' => 'ONE,TWO,THREE',
        ]);

        $this->assertEquals(['ONE','TWO','THREE'], $model->getUserWorkstations());

        $model->userworkstations = 'ONE,';

        $this->assertEquals(['ONE'], $model->getUserWorkstations());

        $model->userworkstations = 'ONE,TWO';

        $this->assertEquals(['ONE','TWO'], $model->getUserWorkstations());
    }

    public function test_get_user_workstations_always_returns_array_when_empty_or_null()
    {
        $model = $this->newUserModel([
            'userworkstations' => null,
        ]);

        $this->assertEquals([], $model->getUserWorkstations());

        $model->userworkstations = '';

        $this->assertEquals([], $model->getUserWorkstations());
    }
}
