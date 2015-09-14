<?php

namespace Adldap\Models;

use Adldap\Classes\Utilities;
use Adldap\Exceptions\AdldapException;
use Adldap\Exceptions\PasswordPolicyException;
use Adldap\Exceptions\WrongPasswordException;
use Adldap\Models\Traits\HasLastLogonAndLogOffTrait;
use Adldap\Models\Traits\HasMemberOfTrait;
use Adldap\Objects\AccountControl;
use Adldap\Schemas\ActiveDirectory;

class User extends Entry
{
    use HasMemberOfTrait;

    use HasLastLogonAndLogOffTrait;

    /**
     * Returns the users display name.
     *
     * @return string
     */
    public function getDisplayName()
    {
        return $this->getAttribute(ActiveDirectory::DISPLAY_NAME, 0);
    }

    /**
     * Sets the users display name.
     *
     * @param string $displayName
     *
     * @return User
     */
    public function setDisplayName($displayName)
    {
        return $this->setAttribute(ActiveDirectory::DISPLAY_NAME, $displayName, 0);
    }

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
     * @return User
     */
    public function setTitle($title)
    {
        return $this->setAttribute(ActiveDirectory::TITLE, $title, 0);
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
     * @return User
     */
    public function setDepartment($department)
    {
        return $this->setAttribute(ActiveDirectory::DEPARTMENT, $department, 0);
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
     * @return User
     */
    public function setFirstName($firstName)
    {
        return $this->setAttribute(ActiveDirectory::FIRST_NAME, $firstName, 0);
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
     * @return User
     */
    public function setLastName($lastName)
    {
        return $this->setAttribute(ActiveDirectory::LAST_NAME, $lastName, 0);
    }

    /**
     * Returns the users initials.
     *
     * @return mixed
     */
    public function getInitials()
    {
        return $this->getAttribute(ActiveDirectory::INITIALS, 0);
    }

    /**
     * Sets the users initials.
     *
     * @param string $initials
     *
     * @return User
     */
    public function setInitials($initials)
    {
        return $this->setAttribute(ActiveDirectory::INITIALS, $initials, 0);
    }

    /**
     * Returns the users country.
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->getAttribute(ActiveDirectory::COUNTRY, 0);
    }

    /**
     * Sets the users country.
     *
     * @param string $country
     *
     * @return User
     */
    public function setCountry($country)
    {
        return $this->setAttribute(ActiveDirectory::COUNTRY, $country, 0);
    }

    /**
     * Returns the users street address.
     *
     * @return User
     */
    public function getStreetAddress()
    {
        return $this->getAttribute(ActiveDirectory::STREET_ADDRESS, 0);
    }

    /**
     * Sets the users street address.
     *
     * @param string $address
     *
     * @return User
     */
    public function setStreetAddress($address)
    {
        return $this->setAttribute(ActiveDirectory::STREET_ADDRESS, $address, 0);
    }

    /**
     * Returns the users postal code.
     *
     * @return string
     */
    public function getPostalCode()
    {
        return $this->getAttribute(ActiveDirectory::POSTAL_CODE, 0);
    }

    /**
     * Sets the users postal code.
     *
     * @param string $postalCode
     *
     * @return User
     */
    public function setPostalCode($postalCode)
    {
        return $this->setAttribute(ActiveDirectory::POSTAL_CODE, $postalCode, 0);
    }

    /**
     * Returns the users physical delivery office name.
     *
     * @return string
     */
    public function getPhysicalDeliveryOfficeName()
    {
        return $this->getAttribute(ActiveDirectory::PHYSICAL_DELIVERY_OFFICE_NAME, 0);
    }

    /**
     * Sets the users physical delivery office name.
     *
     * @param string $deliveryOffice
     *
     * @return User
     */
    public function setPhysicalDeliveryOfficeName($deliveryOffice)
    {
        return $this->setAttribute(ActiveDirectory::PHYSICAL_DELIVERY_OFFICE_NAME, $deliveryOffice, 0);
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
     * @return User
     */
    public function setTelephoneNumber($number)
    {
        return $this->setAttribute(ActiveDirectory::TELEPHONE, $number, 0);
    }

    /**
     * Returns the users locale.
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->getAttribute(ActiveDirectory::LOCALE, 0);
    }

    /**
     * Sets the users locale.
     *
     * @param string $locale
     *
     * @return User
     */
    public function setLocale($locale)
    {
        return $this->setAttribute(ActiveDirectory::LOCALE, $locale, 0);
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
     * @return User
     */
    public function setCompany($company)
    {
        return $this->setAttribute(ActiveDirectory::COMPANY, $company, 0);
    }

    /**
     * Returns the users primary email address.
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
     * @return User
     */
    public function setEmail($email)
    {
        return $this->setAttribute(ActiveDirectory::EMAIL, $email, 0);
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
     * @return User
     */
    public function setEmails(array $emails = [])
    {
        return $this->setAttribute(ActiveDirectory::EMAIL, $emails);
    }

    /**
     * Returns the users other mailbox attribute.
     *
     * https://msdn.microsoft.com/en-us/library/ms679091(v=vs.85).aspx
     *
     * @return array
     */
    public function getOtherMailbox()
    {
        return $this->getAttribute(ActiveDirectory::OTHER_MAILBOX);
    }

    /**
     * Sets the users other mailboxes.
     *
     * @param array $otherMailbox
     *
     * @return User
     */
    public function setOtherMailbox($otherMailbox = [])
    {
        return $this->setAttribute(ActiveDirectory::OTHER_MAILBOX, $otherMailbox);
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
     * Sets the users user principal name.
     *
     * @param string $userPrincipalName
     *
     * @return User
     */
    public function setUserPrincipalName($userPrincipalName)
    {
        return $this->setAttribute(ActiveDirectory::USER_PRINCIPAL_NAME, $userPrincipalName, 0);
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
     * Sets the users account control property.
     *
     * @param int|string|AccountControl $accountControl
     *
     * @return User
     */
    public function setUserAccountControl($accountControl)
    {
        return $this->setAttribute(ActiveDirectory::USER_ACCOUNT_CONTROL, (string) $accountControl);
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
     * Returns the users legacy exchange distinguished name.
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
     * Sets the users account expiry date.
     *
     * @param float $expiryTime
     *
     * @return User
     */
    public function setAccountExpiry($expiryTime)
    {
        return $this->setAttribute(ActiveDirectory::ACCOUNT_EXPIRES, Utilities::convertUnixTimeToWindowsTime($expiryTime), 0);
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
     * Returns the users thumbnail photo.
     *
     * @return mixed
     */
    public function getThumbnail()
    {
        return $this->getAttribute(ActiveDirectory::THUMBNAIL, 0);
    }

    /**
     * Enables the current user.
     *
     * @throws AdldapException
     *
     * @return User
     */
    public function enable()
    {
        $this->enabled = 1;

        return $this;
    }

    /**
     * Disables the current user.
     *
     * @throws AdldapException
     *
     * @return User
     */
    public function disable()
    {
        $this->enabled = 0;

        return $this;
    }

    /**
     * Sets the password on the current user.
     *
     * @param string $password
     *
     * @throws AdldapException
     *
     * @return bool
     */
    public function setPassword($password)
    {
        $connection = $this->query->getConnection();

        if (!$connection->isUsingSSL() && !$connection->isUsingTLS()) {
            $message = 'SSL or TLS must be configured on your web server and enabled to set passwords.';

            throw new AdldapException($message);
        }

        return $this->setModification(ActiveDirectory::UNICODE_PASSWORD, LDAP_MODIFY_BATCH_REPLACE, Utilities::encodePassword($password));
    }

    /**
     * Change the password of the current user. This must be performed over SSL.
     * 
     * @param string $oldPassword The new password
     * @param string $newPassword The old password
     *
     * @throws AdldapException
     * @throws PasswordPolicyException
     * @throws WrongPasswordException
     *
     * @return bool
     */
    public function changePassword($oldPassword, $newPassword)
    {
        $connection = $this->query->getConnection();

        if (!$connection->isUsingSSL() && !$connection->isUsingTLS()) {
            $message = 'SSL or TLS must be configured on your web server and enabled to change passwords.';

            throw new AdldapException($message);
        }

        $attribute = ActiveDirectory::UNICODE_PASSWORD;

        $this->setModification($attribute, LDAP_MODIFY_BATCH_REMOVE, Utilities::encodePassword($oldPassword));
        $this->setModification($attribute, LDAP_MODIFY_BATCH_ADD, Utilities::encodePassword($newPassword));

        $result = $this->update();

        if ($result === false) {
            $error = $connection->getExtendedError();

            if ($error) {
                $errorCode = $connection->getExtendedErrorCode();

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
}
