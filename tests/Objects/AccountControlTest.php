<?php

namespace Adldap\Tests\Objects;

use Adldap\Objects\AccountControl;
use Adldap\Tests\FunctionalTestCase;

class AccountControlTest extends FunctionalTestCase
{
    public function testConstructValue()
    {
        $attributes = array(
            'NORMAL_ACCOUNT' => true,
            'DONT_EXPIRE_PASSWORD' => true,
        );

        $accountControl = new AccountControl($attributes);

        // Code means that the account is enabled, and the users password does not expire
        $code = 66048;

        $this->assertEquals($code, $accountControl->value);
    }
}