<?php

namespace Adldap\Objects;

/**
 * The Account Control class.
 *
 * This class is for easily building a user account control value.
 *
 * https://support.microsoft.com/en-us/kb/305144
 */
class AccountControl
{
    const SCRIPT = 1;

    const ACCOUNTDISABLE = 2;

    const HOMEDIR_REQUIRED = 8;

    const LOCKOUT = 16;

    const PASSWD_NOTREQD = 32;

    const ENCRYPTED_TEXT_PWD_ALLOWED = 128;

    const TEMP_DUPLICATE_ACCOUNT = 256;

    const NORMAL_ACCOUNT = 512;

    const INTERDOMAIN_TRUST_ACCOUNT = 2048;

    const WORKSTATION_TRUST_ACCOUNT = 4096;

    const SERVER_TRUST_ACCOUNT = 8192;

    const DONT_EXPIRE_PASSWORD = 65536;

    const MNS_LOGON_ACCOUNT = 131072;

    const SMARTCARD_REQUIRED = 262144;

    const TRUSTED_FOR_DELEGATION = 524288;

    const NOT_DELEGATED = 1048576;

    const USE_DES_KEY_ONLY = 2097152;

    const DONT_REQ_PREAUTH = 4194304;

    const PASSWORD_EXPIRED = 8388608;

    const TRUSTED_TO_AUTH_FOR_DELEGATION = 16777216;

    const PARTIAL_SECRETS_ACCOUNT = 67108864;

    /**
     * Stores the values to be added together to
     * build the user account control integer.
     *
     * @var array
     */
    protected $values = [];

    /**
     * Constructor.
     *
     * @param int $flag
     */
    public function __construct($flag = null)
    {
        if (!is_null($flag)) {
            $this->apply($flag);
        }
    }

    /**
     * Returns the account control integer as a string
     * when the object is casted as a string.
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getValue();
    }

    /**
     * Returns the account control integer when
     * the object is casted as an integer.
     *
     * @return int
     */
    public function __toInt()
    {
        return $this->getValue();
    }

    /**
     * Applies the specified flag.
     *
     * @param $flag
     */
    public function apply($flag)
    {
        $flags = [];

        for ($i = 0; $i <= 26; $i++) {
            if ((int) $flag & (1 << $i)) {
                array_push($flags, 1 << $i);
            }
        }

        $this->setValues($flags);
    }

    /**
     * The logon script will be run.
     *
     * @return AccountControl
     */
    public function runLoginScript()
    {
        $this->applyValue(self::SCRIPT);

        return $this;
    }

    /**
     * The user account is locked.
     *
     * @return AccountControl
     */
    public function accountIsLocked()
    {
        $this->applyValue(self::LOCKOUT);

        return $this;
    }

    /**
     * The user account is disabled.
     *
     * @return AccountControl
     */
    public function accountIsDisabled()
    {
        $this->applyValue(self::ACCOUNTDISABLE);

        return $this;
    }

    /**
     * This is an account for users whose primary account is in another domain.
     *
     * This account provides user access to this domain, but not to any domain that
     * trusts this domain. This is sometimes referred to as a local user account.
     *
     * @return AccountControl
     */
    public function accountIsTemporary()
    {
        $this->applyValue(self::TEMP_DUPLICATE_ACCOUNT);

        return $this;
    }

    /**
     * This is a default account type that represents a typical user.
     *
     * @return AccountControl
     */
    public function accountIsNormal()
    {
        $this->applyValue(self::NORMAL_ACCOUNT);

        return $this;
    }

    /**
     * This is a permit to trust an account for a system domain that trusts other domains.
     *
     * @return AccountControl
     */
    public function accountIsForInterdomain()
    {
        $this->applyValue(self::INTERDOMAIN_TRUST_ACCOUNT);

        return $this;
    }

    /**
     * This is a computer account for a computer that is running Microsoft
     * Windows NT 4.0 Workstation, Microsoft Windows NT 4.0 Server, Microsoft
     * Windows 2000 Professional, or Windows 2000 Server and is a member of this domain.
     *
     * @return AccountControl
     */
    public function accountIsForWorkstation()
    {
        $this->applyValue(self::WORKSTATION_TRUST_ACCOUNT);

        return $this;
    }

    /**
     * This is a computer account for a domain controller that is a member of this domain.
     *
     * @return AccountControl
     */
    public function accountIsForServer()
    {
        $this->applyValue(self::SERVER_TRUST_ACCOUNT);

        return $this;
    }

    /**
     * This is an MNS logon account.
     *
     * @return AccountControl
     */
    public function accountIsMnsLogon()
    {
        $this->applyValue(self::MNS_LOGON_ACCOUNT);

        return $this;
    }

    /**
     * (Windows 2000/Windows Server 2003) This account does
     * not require Kerberos pre-authentication for logging on.
     *
     * @return AccountControl
     */
    public function accountDoesNotRequirePreAuth()
    {
        $this->applyValue(self::DONT_REQ_PREAUTH);

        return $this;
    }

    /**
     * When this flag is set, it forces the user to log on by using a smart card.
     *
     * @return AccountControl
     */
    public function accountRequiresSmartCard()
    {
        $this->applyValue(self::SMARTCARD_REQUIRED);

        return $this;
    }

    /**
     * (Windows Server 2008/Windows Server 2008 R2) The account is a read-only domain controller (RODC).
     *
     * This is a security-sensitive setting. Removing this setting from an RODC compromises security on that server.
     *
     * @return AccountControl
     */
    public function accountIsReadOnly()
    {
        $this->applyValue(self::PARTIAL_SECRETS_ACCOUNT);

        return $this;
    }

    /**
     * The home folder is required.
     *
     * @return AccountControl
     */
    public function homeFolderIsRequired()
    {
        $this->applyValue(self::HOMEDIR_REQUIRED);

        return $this;
    }

    /**
     * No password is required.
     *
     * @return AccountControl
     */
    public function passwordIsNotRequired()
    {
        $this->applyValue(self::PASSWD_NOTREQD);

        return $this;
    }

    /**
     * The user cannot change the password. This is a permission on the user's object.
     *
     * For information about how to programmatically set this permission, visit the following Web site:
     * http://msdn2.microsoft.com/en-us/library/aa746398.aspx
     *
     * @return AccountControl
     */
    public function passwordCannotBeChanged()
    {
        $this->applyValue(self::PASSWD_NOTREQD);

        return $this;
    }

    /**
     * Represents the password, which should never expire on the account.
     *
     * @return AccountControl
     */
    public function passwordDoesNotExpire()
    {
        $this->applyValue(self::DONT_EXPIRE_PASSWORD);

        return $this;
    }

    /**
     * (Windows 2000/Windows Server 2003) The user's password has expired.
     *
     * @return AccountControl
     */
    public function passwordIsExpired()
    {
        $this->applyValue(self::PASSWORD_EXPIRED);

        return $this;
    }

    /**
     * The user can send an encrypted password.
     *
     * @return AccountControl
     */
    public function allowEncryptedTextPassword()
    {
        $this->applyValue(self::ENCRYPTED_TEXT_PWD_ALLOWED);

        return $this;
    }

    /**
     * When this flag is set, the service account (the user or computer account)
     * under which a service runs is trusted for Kerberos delegation.
     *
     * Any such service can impersonate a client requesting the service.
     *
     * To enable a service for Kerberos delegation, you must set this
     * flag on the userAccountControl property of the service account.
     *
     * @return AccountControl
     */
    public function trustForDelegation()
    {
        $this->applyValue(self::TRUSTED_FOR_DELEGATION);

        return $this;
    }

    /**
     * (Windows 2000/Windows Server 2003) The account is enabled for delegation.
     *
     * This is a security-sensitive setting. Accounts that have this option enabled
     * should be tightly controlled. This setting lets a service that runs under the
     * account assume a client's identity and authenticate as that user to other remote
     * servers on the network.
     *
     * @return AccountControl
     */
    public function trustToAuthForDelegation()
    {
        $this->applyValue(self::TRUSTED_TO_AUTH_FOR_DELEGATION);

        return $this;
    }

    /**
     * When this flag is set, the security context of the user is not delegated to a
     * service even if the service account is set as trusted for Kerberos delegation.
     *
     * @return AccountControl
     */
    public function doNotTrustForDelegation()
    {
        $this->applyValue(self::NOT_DELEGATED);

        return $this;
    }

    /**
     * (Windows 2000/Windows Server 2003) Restrict this principal to
     * use only Data Encryption Standard (DES) encryption types for keys.
     *
     * @return AccountControl
     */
    public function useDesKeyOnly()
    {
        $this->applyValue(self::USE_DES_KEY_ONLY);

        return $this;
    }

    /**
     * Returns the complete account control integer.
     *
     * @return int
     */
    public function getValue()
    {
        $total = 0;

        foreach ($this->values as $value) {
            $total = $total + $value;
        }

        return $total;
    }

    /**
     * Returns the account control's values.
     *
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * Sets the account control values.
     *
     * @param array $flags
     */
    public function setValues(array $flags)
    {
        $this->values = $flags;
    }

    /**
     * Applies the inserted value to the values property array.
     *
     * @param $value
     */
    protected function applyValue($value)
    {
        // Use the value as a key so if the same value
        // is used, it will always be overwritten
        $this->values[$value] = $value;
    }
}
