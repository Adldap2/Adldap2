<?php

namespace Adldap\Classes;

use Adldap\Exceptions\AdldapException;
use Adldap\Objects\Ldap\Entry;
use Adldap\Objects\Ldap\User;
use Adldap\Schemas\ActiveDirectory;

class Users extends AbstractQueryable
{
    /**
     * Returns all users from the current connection.
     *
     * @param array  $fields
     * @param bool   $sorted
     * @param string $sortBy
     * @param string $sortByDirection
     *
     * @return array|bool
     *
     * @throws AdldapException
     */
    public function all($fields = [], $sorted = true, $sortBy = 'cn', $sortByDirection = 'asc')
    {
        $personCategory = $this->adldap->getConfiguration()->getPersonFilter('category');
        $person = $this->adldap->getConfiguration()->getPersonFilter('person');

        $search = $this->adldap->search()
            ->select($fields)
            ->where($personCategory, '=', $person);

        if ($sorted) {
            $search->sortBy($sortBy, $sortByDirection);
        }

        return $search->get();
    }

    /**
     * Finds a user with the specified username
     * in the connection connection.
     *
     * The username parameter can be any attribute on the user.
     * Such as a their name, their logon, their mail, etc.
     *
     * @param string $username
     * @param array  $fields
     *
     * @return array|bool
     */
    public function find($username, $fields = [])
    {
        $personCategory = $this->adldap->getConfiguration()->getPersonFilter('category');
        $person = $this->adldap->getConfiguration()->getPersonFilter('person');

        return $this->adldap->search()
            ->select($fields)
            ->where($personCategory, '=', $person)
            ->where(ActiveDirectory::ANR, '=', $username)
            ->first();
    }

    /**
     * Determine a user's password expiry date.
     *
     * @param $username
     *
     * @return array|string|bool
     *
     * @throws AdldapException
     */
    public function passwordExpiry($username)
    {
        $user = $this->find($username);

        if ($user instanceof User) {
            $passwordLastSet = $user->getPasswordLastSet();

            $status = [
                'expires' => true,
                'has_expired' => false,
            ];

            // Check if the password expires
            if ($user->getUserAccountControl() == '66048') {
                $status['expires'] = false;
            }

            // Check if the password is expired
            if ($passwordLastSet === '0') {
                $status['has_expired'] = true;
            }

            $result = $this->adldap->search()
                ->where(ActiveDirectory::OBJECT_CLASS, '*')
                ->first();

            if ($result instanceof Entry && $status['expires'] === true) {
                $maxPwdAge = $result->getMaxPasswordAge();

                // See MSDN: http://msdn.microsoft.com/en-us/library/ms974598.aspx
                if (bcmod($maxPwdAge, 4294967296) === '0') {
                    return 'Domain does not expire passwords';
                }

                // Add maxpwdage and pwdlastset and we get password expiration time in Microsoft's
                // time units.  Because maxpwd age is negative we need to subtract it.
                $pwdExpire = bcsub($passwordLastSet, $maxPwdAge);

                // Convert MS's time to Unix time
                $unixTime = bcsub(bcdiv($pwdExpire, '10000000'), '11644473600');

                $status['expiry_timestamp'] = $unixTime;
                $status['expiry_formatted'] = date('Y-m-d H:i:s', $unixTime);
            }

            return $status;
        }

        return false;
    }
}
