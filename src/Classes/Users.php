<?php

namespace Adldap\Classes;

use Adldap\Models\Entry;
use Adldap\Models\User;
use Adldap\Exceptions\AdldapException;
use Adldap\Schemas\ActiveDirectory;

class Users extends AbstractBase implements QueryableInterface, CreateableInterface
{
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
     * @return bool|User
     */
    public function find($username, $fields = [])
    {
        return $this->search()->select($fields)->find($username);
    }

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
        $search = $this->search()->select($fields);

        if($sorted) {
            $search->sortBy($sortBy, $sortByDirection);
        }

        return $search->get();
    }

    /**
     * Creates a new search limited to users only.
     *
     * @return Search
     */
    public function search()
    {
        $personCategory = $this->getAdldap()->getConfiguration()->getPersonFilter('category');
        $person = $this->getAdldap()->getConfiguration()->getPersonFilter('person');

        return $this->getAdldap()
            ->search()
            ->where($personCategory, '=', $person);
    }

    /**
     * Creates a new User instance.
     *
     * @param array $attributes
     *
     * @return User
     */
    public function newInstance(array $attributes = [])
    {
        return (new User($attributes, $this->connection))
            ->setAttribute(ActiveDirectory::OBJECT_CLASS, [
                ActiveDirectory::TOP,
                ActiveDirectory::PERSON,
                ActiveDirectory::ORGANIZATIONAL_PERSON,
                ActiveDirectory::USER,
            ]);
    }

    /**
     * Creates and saves a new User instance, then returns the result.
     *
     * @param array $attributes
     *
     * @return bool
     */
    public function create(array $attributes = [])
    {
        return $this->newInstance($attributes)->save();
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
