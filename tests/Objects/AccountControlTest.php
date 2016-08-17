<?php

namespace Adldap\Tests\Objects;

use Adldap\Tests\TestCase;
use Adldap\Objects\AccountControl;

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
        $values = [
            AccountControl::SCRIPT,
            AccountControl::ACCOUNTDISABLE,
            AccountControl::HOMEDIR_REQUIRED,
            AccountControl::LOCKOUT,
            AccountControl::PASSWD_NOTREQD,
            AccountControl::ENCRYPTED_TEXT_PWD_ALLOWED,
            AccountControl::TEMP_DUPLICATE_ACCOUNT,
            AccountControl::NORMAL_ACCOUNT,
            AccountControl::INTERDOMAIN_TRUST_ACCOUNT,
            AccountControl::WORKSTATION_TRUST_ACCOUNT,
            AccountControl::SERVER_TRUST_ACCOUNT,
            AccountControl::DONT_EXPIRE_PASSWORD,
            AccountControl::MNS_LOGON_ACCOUNT,
            AccountControl::SMARTCARD_REQUIRED,
            AccountControl::TRUSTED_FOR_DELEGATION,
            AccountControl::NOT_DELEGATED,
            AccountControl::USE_DES_KEY_ONLY,
            AccountControl::DONT_REQ_PREAUTH,
            AccountControl::PASSWORD_EXPIRED,
            AccountControl::TRUSTED_TO_AUTH_FOR_DELEGATION,
            AccountControl::PARTIAL_SECRETS_ACCOUNT,
        ];

        $ac = new AccountControl();

        $ac->accountIsLocked();
        $ac->accountRequiresSmartCard();
        $ac->accountIsTemporary();
        $ac->accountIsForServer();
        $ac->accountIsForInterdomain();
        $ac->accountIsForWorkstation();
        $ac->accountDoesNotRequirePreAuth();
        $ac->accountIsDisabled();
        $ac->accountIsMnsLogon();
        $ac->accountIsNormal();
        $ac->accountIsReadOnly();

        $ac->allowEncryptedTextPassword();
        $ac->homeFolderIsRequired();
        $ac->passwordCannotBeChanged();
        $ac->passwordDoesNotExpire();
        $ac->passwordIsExpired();
        $ac->passwordIsNotRequired();
        $ac->runLoginScript();
        $ac->trustForDelegation();
        $ac->trustToAuthForDelegation();
        $ac->doNotTrustForDelegation();
        $ac->useDesKeyOnly();

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
}
