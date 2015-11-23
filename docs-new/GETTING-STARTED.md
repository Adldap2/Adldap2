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

Let's move forward to the [connection manager documentation]().
