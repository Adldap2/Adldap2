<?php

namespace Adldap\Tests\Models\Attributes;

use Adldap\Tests\TestCase;
use Adldap\Models\Attributes\AccountControl;

class AccountControlTest extends TestCase
{
    public function test_default_value_is_zero()
    {
        $ac = new AccountControl();

        $this->assertEquals(0, $ac->getValue());
        $this->assertInternalType('int', $ac->getValue());
    }

    public function test_all_options_are_applied_correctly()
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

    public function test_can_be_casted_to_int()
    {
        $ac = new AccountControl();

        $this->assertEquals(0, $ac->__toInt());
        $this->assertEquals(0, (int) $ac->getValue());
        $this->assertInternalType('int', $ac->__toInt());
    }

    public function test_can_be_casted_to_string()
    {
        $ac = new AccountControl();

        $this->assertEquals('0', (string) $ac);
        $this->assertEquals('0', $ac->__toString());
        $this->assertInternalType('string', $ac->__toString());
    }

    public function test_multiple_flags_can_be_applied()
    {
        $flag = 522;

        $ac = new AccountControl($flag);

        $this->assertEquals([
            2   => 2,
            8   => 8,
            512 => 512,
        ], $ac->getValues());
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

    public function test_values_are_overwritten()
    {
        $ac = new AccountControl();

        $ac->accountIsNormal()
            ->accountIsNormal()
            ->accountIsNormal();

        $this->assertEquals(AccountControl::NORMAL_ACCOUNT, $ac->getValue());
    }

    public function test_values_can_be_set()
    {
        $ac = new AccountControl();

        $ac->accountIsNormal()->accountIsDisabled();

        $values = $ac->getValues();

        unset($values[AccountControl::ACCOUNTDISABLE]);

        $ac->setValues($values);

        $this->assertEquals(AccountControl::NORMAL_ACCOUNT, $ac->getValue());
    }

    public function test_values_can_be_added()
    {
        $ac = new AccountControl();

        // Values are overwritten.
        $ac->add(AccountControl::ACCOUNTDISABLE);
        $ac->add(AccountControl::ACCOUNTDISABLE);

        $this->assertEquals(AccountControl::ACCOUNTDISABLE, $ac->getValue());
    }

    public function test_values_can_be_removed()
    {
        $ac = new AccountControl();

        $ac->accountIsNormal()->accountIsDisabled();

        $ac->remove(AccountControl::ACCOUNTDISABLE);

        $this->assertEquals(AccountControl::NORMAL_ACCOUNT, $ac->getValue());

        $ac->remove(AccountControl::NORMAL_ACCOUNT);
        $ac->remove(AccountControl::NORMAL_ACCOUNT);
        $this->assertEquals(0, $ac->getValue());
    }

    public function test_extracted_flags_are_properly_set()
    {
        $ac = new AccountControl(AccountControl::ACCOUNTDISABLE + AccountControl::NORMAL_ACCOUNT);
        $ac->accountIsNormal();

        $this->assertEquals([
            AccountControl::ACCOUNTDISABLE  => AccountControl::ACCOUNTDISABLE,
            AccountControl::NORMAL_ACCOUNT  => AccountControl::NORMAL_ACCOUNT,
        ], $ac->getValues());
    }
}
