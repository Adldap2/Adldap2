<?php

namespace Adldap\Objects;

/**
 * Class AccountControl.
 */
class AccountControl extends AbstractObject
{
    /**
     * Constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setAttribute('value', 0);

        $this->setValueIfAttributeExists('SCRIPT', 1);

        $this->setValueIfAttributeExists('ACCOUNTDISABLE', 2);

        $this->setValueIfAttributeExists('HOMEDIR_REQUIRED', 8);

        $this->setValueIfAttributeExists('LOCKOUT', 16);

        $this->setValueIfAttributeExists('PASSWD_NOTREQD', 32);

        //PASSWD_CANT_CHANGE Note You cannot assign this permission by directly modifying the UserAccountControl attribute.
        //For information about how to set the permission programmatically, see the "Property flag descriptions" section.
        $this->setValueIfAttributeExists('ENCRYPTED_TEXT_PWD_ALLOWED', 128);

        $this->setValueIfAttributeExists('TEMP_DUPLICATE_ACCOUNT', 256);

        $this->setValueIfAttributeExists('NORMAL_ACCOUNT', 512);

        $this->setValueIfAttributeExists('INTERDOMAIN_TRUST_ACCOUNT', 2048);

        $this->setValueIfAttributeExists('WORKSTATION_TRUST_ACCOUNT', 4096);

        $this->setValueIfAttributeExists('SERVER_TRUST_ACCOUNT', 8192);

        $this->setValueIfAttributeExists('DONT_EXPIRE_PASSWORD', 65536);

        $this->setValueIfAttributeExists('MNS_LOGON_ACCOUNT', 131072);

        $this->setValueIfAttributeExists('SMARTCARD_REQUIRED', 262144);

        $this->setValueIfAttributeExists('TRUSTED_FOR_DELEGATION', 524288);

        $this->setValueIfAttributeExists('NOT_DELEGATED', 1048576);

        $this->setValueIfAttributeExists('USE_DES_KEY_ONLY', 2097152);

        $this->setValueIfAttributeExists('DONT_REQ_PREAUTH', 4194304);

        $this->setValueIfAttributeExists('PASSWORD_EXPIRED', 8388608);

        $this->setValueIfAttributeExists('TRUSTED_TO_AUTH_FOR_DELEGATION', 16777216);
    }

    /**
     * If the specified $attribute exists, the 'value' attribute
     * is updated by the specified $value parameter by adding the
     * current 'value' attribute to it.
     *
     * @param $attribute
     * @param $value
     */
    public function setValueIfAttributeExists($attribute, $value)
    {
        if ($this->hasAttribute($attribute)) {
            $currentValue = $this->getAttribute('value');

            $this->setAttribute('value', $currentValue + $value);
        }
    }
}
