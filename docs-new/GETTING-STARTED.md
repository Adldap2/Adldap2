# Getting Started

## Connecting

After setting up a configuration, you can connect to your LDAP server.

```php
$ad = new \Adldap\Adldap($configuration);

try {
    $manager = $ad->connect();
    
    // We connected and bound successfully to the server. Commence dance party.
} catch (\Adldap\Exceptions\ConnectionException $e) {
    // Hmm it looks like we weren't able to contact the server.
} catch (\Adldap\Exceptions\Auth\BindException $e) {
    // We were able to connect to the server, but the administrator credentials are incorrect.
}
```

#### Connecting with different credentials

To connect with alternate credentials, pass a username and password into the `connect()` method:

```php
$manager = $ad->connect('username', 'password123');
```

## After you connect

Once you've connected, a `Adldap\Connections\Manager` instance is returned.
This is the object you'll be performing LDAP operations upon.

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

