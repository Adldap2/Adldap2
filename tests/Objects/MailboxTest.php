<?php

namespace Adldap\Tests\Objects;

use Adldap\Objects\Mailbox;
use Adldap\Tests\FunctionalTestCase;

class MailboxTest extends FunctionalTestCase
{
    public function testValidationPasses()
    {
        $attributes = [
            'username' => 'Jdoe',
            'storageGroup' => array('Test'),
            'emailAddress' => 'jdoe@email.com',
        ];

        $mailbox = new Mailbox($attributes);

        $this->assertTrue($mailbox->validateRequired());
    }

    public function testValidationFails()
    {
        $attributes = [
            'username' => 'Jdoe',
            'emailAddress' => 'jdoe@email.com',
        ];

        $mailbox = new Mailbox($attributes);

        $this->setExpectedException('Adldap\Exceptions\AdldapException');

        $mailbox->validateRequired();
    }

    public function testValidationFailsStorageGroup()
    {
        $attributes = [
            'username' => 'Jdoe',
            'storageGroup' => 'Invalid Storage Group',
            'emailAddress' => 'jdoe@email.com',
        ];

        $mailbox = new Mailbox($attributes);

        $this->setExpectedException('Adldap\Exceptions\AdldapException');

        $mailbox->validateRequired();
    }

    public function testToLdapArray()
    {
        $attributes = [
            'username' => 'Jdoe',
            'storageGroup' => 'Test',
            'emailAddress' => 'jdoe@email.com',
        ];

        $mailbox = new Mailbox($attributes);

        $expected = [
            'exchange_homemdb' => ',',
            'exchange_proxyaddress' => 'SMTP:jdoe@email.com',
            'exchange_mailnickname' => null,
            'exchange_usedefaults' => null,
        ];

        $this->assertEquals($expected, $mailbox->toLdapArray());
    }
}