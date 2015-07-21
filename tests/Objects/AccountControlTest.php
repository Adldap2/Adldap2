<?php

namespace Adldap\Tests\Objects;

use Adldap\Objects\AccountControl;
use Adldap\Tests\FunctionalTestCase;

class AccountControlTest extends FunctionalTestCase
{
    public function testEndingValue()
    {
        $accountControl = new AccountControl();

        $accountControl->accountIsNormal();
        $accountControl->passwordDoesNotExpire();

        // Code means that the account is enabled, and the users password does not expire
        $code = 66048;

        $this->assertEquals($code, $accountControl->getValue());
    }
}