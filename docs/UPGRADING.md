## Upgrading to 5.0.0

Breaking changes:

- Requires have been removed from all classes, and if using without an auto-loader (such as composer) you must require all
necessary files yourself
- `adLDAP\adLDAPException` now has a new namespace: `adLDAP\Exceptions\adLDAPException`
- `$adldap->user()->modify()` now throws an `adLDAPException` when the username parameter is null
- Inserting null/empty values into the username and/or password inside the `authenticate($username, $password)` function will now
result in an `adLDAPException`, instead of returning false
- Inserting null into the group name parameter inside the method `$adLDAP->group()->info($groupName)` now throws an adLDAP exception
instead of returning false
- Inserting null into the username parameter inside the method `$adLDAP->user()->info($username)` now throws an adLDAP exception
instead of returning false
- If LDAP is not bound when running query methods (such as `$adLDAP->search()`) then an `adLDAPException` will be thrown instead
of previously returning false.
- `pingController()` method removed


The arguments for finding a user has been changed from:

    $adldap->user()->find($includeDescription = false, $searchField = false, $searchFilter = false, $sorted = true)

To:

    $adldap->user()->find($includeDescription = false, $searchArray = array(), $sorted = true))
    
This allows you to search for multiple parameters when looking for a user. [Thanks To](https://github.com/adldap/adLDAP/pull/17)


$adldap->group()->search($sAMAaccountType = Adldap::ADLDAP_SECURITY_GLOBAL_GROUP, $includeDescription = false, $search = "*", $sorted = true);

Has Changed to:

$adldap->group()->search($sAMAaccountType = Adldap::ADLDAP_SECURITY_GLOBAL_GROUP, $select = array(), $sorted = true);

Removed function $adldap->group()->cn();