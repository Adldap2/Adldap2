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