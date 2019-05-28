<?php

namespace Adldap\Tests\Models\Attributes;

use Adldap\Tests\TestCase;
use Adldap\Models\Attributes\AccountControl;

class AccountControlTest extends TestCase
{
    public function test_default_value()
    {
        $ac = new AccountControl();

        $this->assertEquals(0, $ac->getValue());
        $this->assertInternalType('int', $ac->getValue());
    }

    public function test_all_options()
    {
        $ac = new AccountControl();

        $values = array_values($ac->getAllFlags());

        $ac
            ->accountIsLocked()
            ->accountRequiresSmartCard()
            ->accountIsTemporary()
            ->accountIsForServer()
            ->accountIsForInterdomain()
            ->accountIsForWorkstation()
            ->accountDoesNotRequirePreAuth()
            ->accountIsDisabled()
            ->accountIsMnsLogon()
            ->accountIsNormal()
            ->accountIsReadOnly()
            ->allowEncryptedTextPassword()
            ->homeFolderIsRequired()
            ->passwordCannotBeChanged()
            ->passwordDoesNotExpire()
            ->passwordIsExpired()
            ->passwordIsNotRequired()
            ->runLoginScript()
            ->trustForDelegation()
            ->trustToAuthForDelegation()
            ->doNotTrustForDelegation()
            ->useDesKeyOnly();

        $this->assertEquals(array_sum($values), $ac->getValue());
    }

    public function test_to_int()
    {
        $ac = new AccountControl();

        $this->assertEquals(0, $ac->__toInt());
        $this->assertInternalType('int', $ac->__toInt());
    }

    public function test_to_string()
    {
        $ac = new AccountControl();

        $this->assertEquals('0', $ac->__toString());
        $this->assertInternalType('string', $ac->__toString());
    }

    public function test_construct()
    {
        $flag = 522;

        $ac = new AccountControl($flag);

        $values = [
            2, 8, 512,
        ];

        $this->assertEquals($values, $ac->getValues());
        $this->assertEquals($flag, $ac->getValue());
    }

    public function test_has()
    {
        $ac = new AccountControl();

        $ac
            ->accountIsLocked()
            ->passwordDoesNotExpire();

        $this->assertTrue($ac->has(AccountControl::LOCKOUT));
        $this->assertTrue($ac->has(AccountControl::DONT_EXPIRE_PASSWORD));
        $this->assertFalse($ac->has(AccountControl::ACCOUNTDISABLE));
        $this->assertFalse($ac->has(AccountControl::ENCRYPTED_TEXT_PWD_ALLOWED));
        $this->assertFalse($ac->has(AccountControl::NORMAL_ACCOUNT));
        $this->assertFalse($ac->has(AccountControl::PASSWD_NOTREQD));
    }
}
