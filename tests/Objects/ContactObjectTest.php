<?php

namespace Adldap\Tests\Objects;

use Adldap\Objects\Contact;
use Adldap\Tests\FunctionalTestCase;

class ContactObjectTest extends FunctionalTestCase
{
    public function testContactObjectValidatePasses()
    {
        $attributes = array(
            'display_name' => 'Display Name',
            'email' => 'Email',
            'container' => array('Child', 'Parent'),
        );

        $contact = new Contact($attributes);

        $this->assertTrue($contact->validateRequired());
    }

    public function testContactObjectValidateContainerFailure()
    {
        $attributes = array(
            'display_name' => 'Display Name',
            'email' => 'Email',
            'container' => 'Invalid Container',
        );

        $contact = new Contact($attributes);

        $this->setExpectedException('Adldap\Exceptions\AdldapException');

        $contact->validateRequired();
    }

    public function testContactObjectValidateOtherAttributesFailure()
    {
        $attributes = array(
            'container' => array('Child', 'Parent')
        );

        $contact = new Contact($attributes);

        $this->setExpectedException('Adldap\Exceptions\AdldapException');

        $contact->validateRequired();
    }
}