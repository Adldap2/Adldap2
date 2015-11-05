<?php

namespace Adldap\Scopes;

use Adldap\Exceptions\AdldapException;
use Adldap\Models\Entry;
use Adldap\Models\User;
use Adldap\Schemas\ActiveDirectory;
use Adldap\Schemas\Schema;

class Users extends AbstractScope implements QueryableInterface, CreateableInterface
{
    /**
     * Finds a user with the specified username.
     *
     * @param string $username
     * @param array  $fields
     *
     * @return bool|User
     */
    public function find($username, $fields = [])
    {
        return $this->search()
            ->select($fields)
            ->whereEquals(Schema::get()->accountName(), $username)
            ->first();
    }

    /**
     * Returns all users from the current connection.
     *
     * @param array  $fields
     * @param bool   $sorted
     * @param string $sortBy
     * @param string $sortDirection
     *
     * @throws AdldapException
     *
     * @return array|bool
     */
    public function all($fields = [], $sorted = true, $sortBy = ActiveDirectory::COMMON_NAME, $sortDirection = 'asc')
    {
        $search = $this->search()->select($fields);

        if ($sorted) {
            $search->sortBy($sortBy, $sortDirection);
        }

        return $search->get();
    }

    /**
     * Creates a new search limited to users only.
     *
     * @return \Adldap\Query\Builder
     */
    public function search()
    {
        $schema = Schema::get();

        return $this
            ->getManager()
            ->search()
            ->whereEquals($schema->objectCategory(), $schema->person());
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
        $schema = Schema::get();

        return (new User($attributes, $this->search()))
            ->setAttribute($schema->objectClass(), [
                $schema->top(),
                $schema->person(),
                $schema->organizationalPerson(),
                $schema->user(),
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
     * @throws AdldapException
     *
     * @return array|string|bool
     */
    public function passwordExpiry($username)
    {
        $user = $this->find($username);

        if ($user instanceof User) {
            $passwordLastSet = $user->getPasswordLastSet();

            $status = [
                'expires'     => true,
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

            $result = $this
                ->getManager()
                ->search()
                ->whereHas(Schema::get()->objectClass())
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
