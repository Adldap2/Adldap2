# Upgrade Guide

## Upgrading to v6 from v5.2

Due to the `v6` being a new major release, large changes have occurred. You will need to modify your code accordingly.

##### Adldap\Adldap Class

Adldap now supports multiple LDAP connections at once. The `Adldap\Adldap` class is now a "Gateway" to multiple connections.

You now construct an Adldap instance, and then attach connection providers (`Adldap\Connections\Provider`) to it.

For example:

```php
$provider = new \Adldap\Connections\Provider($config, $connection, $schema);

$ad = new Adldap();

$ad->addProvider('provider-name', $provider);

$ad->connect('provider-name');
```

##### Search Classes Removed / Replaced with Search Factory

All search classes have been removed and replaced with query 'scopes' utilized in `Adldap\Search\Factory`.

For example, you used to call:

```php
// v5.2
$ad = new Adldap\Adldap($config);

$ad->users()->all();
```

In `v6` you would call:

```php
// v6.0
$provider = $ad->connect('provider-name');

$provider->search()->users()->get();
```

A `Adldap\Search\Factory` instance is returned when calling the `search()` method on your connection provider.
Inside this factory, you can utilize the many scopes for only retrieving certain records (such as Computers or Users).

Please take a look at the [Query Builder documentation](docs/query-builder.md#scopes) for all of the methods.

##### Authentication & Binding Changes

To check a users credentials using your AD server, you used to be able to perform:

```php
// v5.2
$ad = new \Adldap\Adldap($config);

$ad->authenticate($username, $password, $bindAsUser = false);
```

Now you need to utilize the `Adldap\Auth\Guard` object of checking user credentials.
This object is returned when calling the `auth()` method on your connection provider. For example:

```php
// v6.0
if ($provider->auth()->attempt($username, $password, $bindAsUser = false)) {
    // Credentials were valid!
}
```

You can now also bind users manually if you wish, bypassing the empty `username` and `password` validation:

```php
try {
    $provider->auth()->bind($username, $password);

    // User successfully bound.
} catch (\Adldap\Exceptions\Auth\BindException $e) {
    // Uh oh, there was an issue with the users credentials!
}
```

Or you can also manually bind as your configured administrator:

```php
try {
    $provider->auth()->bindAsAdministrator();

    // Admin successfully bound.
} catch (\Adldap\Exceptions\Auth\BindException $e) {
    // Your administrator credentials are incorrect.
}
```

###### Search Results

Search results now return a Laravel collection (`Illuminate\Support\Collection`)
instead of a Doctrine collection (`Doctrine\Common\Collections\ArrayCollection`).

This allows much more flexibility and offers many more handy methods than doctrine collections.

###### Dropped SSO Support

SSO support was available but very un-tested in the root [Adldap2\Adldap2](https://github.com/Adldap2/Adldap2) repository.
This is now dropped, but is now available in the [Adldap2\Adldap2-Laravel](https://github.com/Adldap2/Adldap2-Laravel) repository.

###### Renamed Adldap Interface

The interface `Adldap\Contracts\Adldap` has now been renamed to `Adldap\Contracts\AdldapInterface`.

## More

If you encounter anything that isn't covered here, please create an issue or submit a pull-request.
