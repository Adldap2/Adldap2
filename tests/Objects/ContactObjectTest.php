<?php

namespace adLDAP\Tests\Objects;

use adLDAP\Objects\Contact;
use adLDAP\Tests\FunctionalTestCase;

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

        $this->setExpectedException('adLDAP\Exceptions\adLDAPException');

        $contact->validateRequired();
    }

    public function testContactObjectValidateOtherAttributesFailure()
    {
        $attributes = array(
            'container' => array('Child', 'Parent')
        );

        $contact = new Contact($attributes);

        $this->setExpectedException('adLDAP\Exceptions\adLDAPException');

        $contact->validateRequired();
    }
}