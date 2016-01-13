# Connection Manager

## Authentication

### Authenticating a user

Performing authentication with Adldap2 will bind to your LDAP server under the inserted username and password, **then
rebind under your configured administrator**. It does not set any session variables or perform a magic 
show behind the scenes.

```php
$manager = $ad->connect();

if ($manager->auth()->attempt($username, $password)) {
    // Authentication passed!
} else {
    // Authentication failed, credentials were incorrect.
}
```

When authenticating a user, if re-binding as your configured administrator fails, a `BindException` will be thrown:

```php
$manager = $ad->connect();

try {
    if ($manager->auth()->attempt($username, $password)) {
        // Authentication passed!
    } else {
        // Authentication failed, credentials were incorrect.
    }   
} catch (\Adldap\Exceptions\Auth\BindException $e) {
    // Rebinding as the administrator failed.
}
```

### Binding as the authenticated user

If you'd like to bind to your server as the user if authentication passes, pass in `true` into the third parameter.

> **Note**: Binding as the authenticated user means all operations on the LDAP server will be run under the user.

```php
$manager = $ad->connect();

if ($manager->auth()->attempt($username, $password, $bindAsUser = true)) {
    // Authentication passed!
} else {
    // Authentication failed, credentials were incorrect.
}
```

## Search

Searching is a big part of LDAP, and Adldap2 makes it really easy to find exactly what you're looking for.

Call the `search()` method on the connection `Manager` to open a new search:

> **Note**: Calling the `search()` method returns a new instance of `Adldap\Search\Factory`.

```php
$search = $manager->search();
```

Calling methods that do not exist on the search object, will be called upon a new Query builder instance.

### Create a new query

To generate a new query, call the `newQuery()` method:

```php
$query = $search->newQuery();
```

The `newQuery()` method also accepts a `$baseDn` argument so set the base DN of your search:

```php
$baseDn = 'dc=corp,dc=org';

$query = $search->newQuery($baseDn);
```

> **Note**: Upon construct, the search Factory will have the base DN of it's query set to your configured base DN.

### Get the current query

To retrieve the current query on the search Factory, use the `getQuery()` method:

```php
$query = $search->getQuery();
```

### Setting the search query

To set the query on the search Factory, call the `setQuery()` method:

```php
$query = $search->newQuery($baseDn);

$search->setQuery($query);
```

All methods on the search Factory will be ran on the current query.

## Query Builder

Now you know how to construct a new query, lets move on to the [Query Builder Documentation]().
