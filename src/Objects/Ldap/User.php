<?php

namespace Adldap\Objects\Ldap;

use Adldap\Exceptions\AdldapException;
use Adldap\Exceptions\PasswordPolicyException;
use Adldap\Exceptions\WrongPasswordException;
use Adldap\Schemas\ActiveDirectory;
use Adldap\Objects\Traits\HasLastLogonAndLogOffTrait;
use Adldap\Objects\Traits\HasMemberOfTrait;

class User extends Entry
{
    use HasMemberOfTrait;

    use HasLastLogonAndLogOffTrait;

    /**
     * Returns the users title.
     *
     * https://msdn.microsoft.com/en-us/library/ms680037(v=vs.85).aspx
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getAttribute(ActiveDirectory::TITLE, 0);
    }

    /**
     * Sets the users title.
     *
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        return $this->setAttribute(ActiveDirectory::TITLE, $title);
    }

    /**
     * Returns the users department.
     *
     * https://msdn.microsoft.com/en-us/library/ms675490(v=vs.85).aspx
     *
     * @return string
     */
    public function getDepartment()
    {
        return $this->getAttribute(ActiveDirectory::DEPARTMENT, 0);
    }

    /**
     * Sets the users department.
     *
     * @param string $department
     *
     * @return $this
     */
    public function setDepartment($department)
    {
        return $this->setAttribute(ActiveDirectory::DEPARTMENT, $department);
    }

    /**
     * Returns the users first name.
     *
     * https://msdn.microsoft.com/en-us/library/ms675719(v=vs.85).aspx
     *
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->getAttribute(ActiveDirectory::FIRST_NAME, 0);
    }

    /**
     * Sets the users first name.
     *
     * @param string $firstName
     *
     * @return $this
     */
    public function setFirstName($firstName)
    {
        return $this->setAttribute(ActiveDirectory::FIRST_NAME, $firstName);
    }

    /**
     * Returns the users last name.
     *
     * https://msdn.microsoft.com/en-us/library/ms679872(v=vs.85).aspx
     *
     * @return mixed
     */
    public function getLastName()
    {
        return $this->getAttribute(ActiveDirectory::LAST_NAME, 0);
    }

    /**
     * Sets the users last name.
     *
     * @param string $lastName
     *
     * @return $this
     */
    public function setLastName($lastName)
    {
        return $this->setAttribute(ActiveDirectory::LAST_NAME, $lastName);
    }

    /**
     * Returns the users telephone number.
     *
     * https://msdn.microsoft.com/en-us/library/ms680027(v=vs.85).aspx
     *
     * @return string
     */
    public function getTelephoneNumber()
    {
        return $this->getAttribute(ActiveDirectory::TELEPHONE, 0);
    }

    /**
     * Sets the users telephone number.
     *
     * @param string $number
     *
     * @return $this
     */
    public function setTelephoneNumber($number)
    {
        return $this->setAttribute(ActiveDirectory::TELEPHONE, $number);
    }

    /**
     * Returns the users company.
     *
     * https://msdn.microsoft.com/en-us/library/ms675457(v=vs.85).aspx
     *
     * @return string
     */
    public function getCompany()
    {
        return $this->getAttribute(ActiveDirectory::COMPANY, 0);
    }

    /**
     * Sets the users company.
     *
     * @param string $company
     *
     * @return $this
     */
    public function setCompany($company)
    {
        return $this->setAttribute(ActiveDirectory::COMPANY, $company);
    }

    /**
     * Returns the users first email address.
     *
     * https://msdn.microsoft.com/en-us/library/ms676855(v=vs.85).aspx
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->getAttribute(ActiveDirectory::EMAIL, 0);
    }

    /**
     * Sets the users email.
     *
     * Keep in mind this will remove all other
     * email addresses the user currently has.
     *
     * @param string $email
     *
     * @return $this
     */
    public function setEmail($email)
    {
        return $this->setAttribute(ActiveDirectory::EMAIL, $email);
    }

    /**
     * Returns the users email addresses.
     *
     * https://msdn.microsoft.com/en-us/library/ms676855(v=vs.85).aspx
     *
     * @return array
     */
    public function getEmails()
    {
        return $this->getAttribute(ActiveDirectory::EMAIL);
    }

    /**
     * Sets the users email addresses.
     *
     * @param array $emails
     *
     * @return $this
     */
    public function setEmails(array $emails)
    {
        return $this->setAttribute(ActiveDirectory::EMAIL, $emails);
    }

    /**
     * Returns the users mailbox store DN.
     *
     * https://msdn.microsoft.com/en-us/library/aa487565(v=exchg.65).aspx
     *
     * @return string
     */
    public function getHomeMdb()
    {
        return $this->getAttribute(ActiveDirectory::HOME_MDB, 0);
    }

    /**
     * Returns the users mail nickname.
     *
     * @return string
     */
    public function getMailNickname()
    {
        return $this->getAttribute(ActiveDirectory::EMAIL_NICKNAME, 0);
    }

    /**
     * Returns the users principal name.
     *
     * This is usually their email address.
     *
     * https://msdn.microsoft.com/en-us/library/ms680857(v=vs.85).aspx
     *
     * @return string
     */
    public function getUserPrincipalName()
    {
        return $this->getAttribute(ActiveDirectory::USER_PRINCIPAL_NAME, 0);
    }

    /**
     * Returns the users proxy addresses.
     *
     * https://msdn.microsoft.com/en-us/library/ms679424(v=vs.85).aspx
     *
     * @return array
     */
    public function getProxyAddresses()
    {
        return $this->getAttribute(ActiveDirectory::PROXY_ADDRESSES);
    }

    /**
     * Returns the users script path if the user has one.
     *
     * https://msdn.microsoft.com/en-us/library/ms679656(v=vs.85).aspx
     *
     * @return string
     */
    public function getScriptPath()
    {
        return $this->getAttribute(ActiveDirectory::SCRIPT_PATH, 0);
    }

    /**
     * Returns the users bad password count.
     *
     * @return string
     */
    public function getBadPasswordCount()
    {
        return $this->getAttribute(ActiveDirectory::BAD_PASSWORD_COUNT, 0);
    }

    /**
     * Returns the users bad password time.
     *
     * @return string
     */
    public function getBadPasswordTime()
    {
        return $this->getAttribute(ActiveDirectory::BAD_PASSWORD_TIME, 0);
    }

    /**
     * Returns the time when the users password was set last.
     *
     * @return string
     */
    public function getPasswordLastSet()
    {
        return $this->getAttribute(ActiveDirectory::PASSWORD_LAST_SET, 0);
    }

    /**
     * Returns the users lockout time.
     *
     * @return string
     */
    public function getLockoutTime()
    {
        return $this->getAttribute(ActiveDirectory::LOCKOUT_TIME, 0);
    }

    /**
     * Returns the users user account control integer.
     *
     * @return string
     */
    public function getUserAccountControl()
    {
        return $this->getAttribute(ActiveDirectory::USER_ACCOUNT_CONTROL, 0);
    }

    /**
     * Returns the users profile file path.
     *
     * @return string
     */
    public function getProfilePath()
    {
        return $this->getAttribute(ActiveDirectory::PROFILE_PATH, 0);
    }

    /**
     * Returns the users legaxy exchange distinguished name.
     *
     * @return string
     */
    public function getLegacyExchangeDn()
    {
        return $this->getAttribute(ActiveDirectory::LEGACY_EXCHANGE_DN, 0);
    }

    /**
     * Returns the users account expiry date.
     *
     * @return string
     */
    public function getAccountExpiry()
    {
        return $this->getAttribute(ActiveDirectory::ACCOUNT_EXPIRES, 0);
    }

    /**
     * Returns an array of address book DNs
     * that the user is listed to be shown in.
     *
     * @return array
     */
    public function getShowInAddressBook()
    {
        return $this->getAttribute(ActiveDirectory::SHOW_IN_ADDRESS_BOOK);
    }

    /**
     * Enables the current user.
     *
     * @return bool
     *
     * @throws AdldapException
     */
    public function enable()
    {
        $this->enabled = 1;

        return $this->save();
    }

    /**
     * Disables the current user.
     *
     * @return bool
     *
     * @throws AdldapException
     */
    public function disable()
    {
        $this->enabled = 0;

        return $this->save();
    }

    /**
     * Sets the password on the current user.
     *
     * @param string $password
     *
     * @return bool
     *
     * @throws AdldapException
     */
    public function setPassword($password)
    {
        if (!$this->connection->isUsingSSL() && !$this->connection->isUsingTLS()) {
            $message = 'SSL or TLS must be configured on your web server and enabled to set passwords.';

            throw new AdldapException($message);
        }

        $this->setModification(ActiveDirectory::UNICODE_PASSWORD, LDAP_MODIFY_BATCH_ADD, $this->encodePassword($password));

        $result = $this->save();

        if ($result === false) {
            $err = $this->connection->errNo();

            if ($err) {
                $error = $this->connection->err2Str($err);

                $msg = 'Error '.$err.': '.$error.'.';

                if ($err == 53) {
                    $msg .= ' Your password might not match the password policy.';
                }

                throw new AdldapException($msg);
            } else {
                return false;
            }
        }

        return $result;
    }

    /**
     * Change the password of the current user. This must be performed over SSL.

     * @param string $oldPassword The new password
     * @param string $newPassword The old password
     *
     * @return bool
     *
     * @throws AdldapException
     * @throws PasswordPolicyException
     * @throws WrongPasswordException
     */
    public function changePassword($oldPassword, $newPassword)
    {
        if (!$this->connection->isUsingSSL() && !$this->connection->isUsingTLS()) {
            $message = 'SSL or TLS must be configured on your web server and enabled to change passwords.';

            throw new AdldapException($message);
        }

        $attribute = ActiveDirectory::UNICODE_PASSWORD;

        $this->setModification($attribute, LDAP_MODIFY_BATCH_REMOVE, $this->encodePassword($oldPassword));
        $this->setModification($attribute, LDAP_MODIFY_BATCH_ADD, $this->encodePassword($newPassword));

        $result = $this->save();

        if($result === false) {
            $error = $this->connection->getExtendedError();

            if ($error) {
                $errorCode = $this->connection->getExtendedErrorCode();

                $message = 'Error: '.$error;

                if ($errorCode == '0000052D') {
                    $message = "Error: $errorCode. Your new password might not match the password policy.";

                    throw new PasswordPolicyException($message);
                } elseif ($errorCode == '00000056') {
                    $message = "Error: $errorCode. Your old password might be wrong.";

                    throw new WrongPasswordException($message);
                }

                throw new AdldapException($message);
            } else {
                return false;
            }
        }

        return $result;
    }

    /**
     * Encode a password for transmission over LDAP.
     *
     * @param string $password The password to encode
     *
     * @return string
     */
    private function encodePassword($password)
    {
        $password = '"'.$password.'"';

        $encoded = '';

        $length = strlen($password);

        for ($i = 0; $i < $length; $i++) {
            $encoded .= "{$password{$i}
            }\000";
        }

        return $encoded;
    }
}
