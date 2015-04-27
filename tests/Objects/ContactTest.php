<?php

namespace Adldap\Tests\Objects;

use Adldap\Objects\Contact;
use Adldap\Tests\FunctionalTestCase;

class ContactTest extends FunctionalTestCase
{
    public function testContactObjectValidatePasses()
    {
        $attributes = [
            'display_name' => 'Display Name',
            'email' => 'Email',
            'container' => ['Child', 'Parent'],
        ];

        $contact = new Contact($attributes);

        $this->assertTrue($contact->validateRequired());
    }

    public function testContactObjectValidateContainerFailure()
    {
        $attributes = [
            'display_name' => 'Display Name',
            'email' => 'Email',
            'container' => 'Invalid Container',
        ];

        $contact = new Contact($attributes);

        $this->setExpectedException('Adldap\Exceptions\AdldapException');

        $contact->validateRequired();
    }

    public function testContactObjectValidateOtherAttributesFailure()
    {
        $attributes = [
            'container' => ['Child', 'Parent']
        ];

        $contact = new Contact($attributes);

        $this->setExpectedException('Adldap\Exceptions\AdldapException');

        $contact->validateRequired();
    }
}