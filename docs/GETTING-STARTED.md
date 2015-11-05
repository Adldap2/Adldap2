# Getting Started

## Connecting

To connect to your server, call the `connect()` method:

```php
try {
    // Construct a new Adldap instance
    $ad = new Adldap($config);
    
    // Connect to your LDAP server
    $ad->connect();
    
    // Successfully bound to server!
} catch (Adldap\Exceptions\Auth\BindException $e) {
    // Binding Failed!
} catch (Adldap\Exceptions\ConnectionException $e) {
    // Couldn't connect to the LDAP server!
}
```

If you would like to connect and bind with different credentials than your configuration, insert a username and password as
the first and second paramter in the `connect()` method:

```php
try {
    $ad = new Adldap($config);
     
    $ad->connect('admin', 'password123');

    // Successfully bound to server!
} catch (Adldap\Exceptions\Auth\BindException $e) {
    // Binding Failed!
} catch (Adldap\Exceptions\ConnectionException $e) {
    // Couldn't connect to the LDAP server!
}
```

## Authenticating Users

To authenticate a user against your a AD server, use the `auth()->attempt($username, $password)` method on the Adldap instance:

```php
try {
    if ($adldap->auth()->attempt($username, $password)) {
        
        // Authentication Passed!
        
    } else {
        
        // Authentication Failed!
        
    }
    
    // Authentication Passed!
} catch (Adldap\Exceptions\Auth\UsernameRequiredException $e) {

    // Username is required to authenticate!
    
} catch (Adldap\Exceptions\Auth\PasswordRequiredException $e) {

    // Password is required to authenticate!
    
} catch (Adldap\Exceptions\Auth\BindException $e) {

    // Rebind to LDAP server as Administrator failed!
    
}
```

If you'd like to *bind* the authenticated user to your AD server so all operations ran through Adldap are run as the authenticated user,
pass in `true` into the third parameter:

```php

try {
    if ($adldap->auth()->attempt($username, $password, true)) {
        
        // Authentication Passed!
        
    } else {
        
        // Authentication Failed!
        
    }
} catch (Adldap\Exceptions\Auth\UsernameRequiredException $e) {

  // Username is required to authenticate!
  
} catch (Adldap\Exceptions\Auth\PasswordRequiredException $e) {

  // Password is required to authenticate!
  
}
```

> **Note**: Keep in mind if you authenticate but not bind the user to your server, all operations will be
ran under your configured administrator credentials.

## Changing Connections

If you'd like to create your own custom Connection class, use the `setConnection()` method or insert it into the
second parameter when you're constructing a new Adldap instance:

```php
// CustomConnection.php
class CustomConnection extends Adldap\Connections\Ldap
{
    //
}
```

Then in some other file:

```php
// Inserting the connection in the second parameter
$ad = new Adldap($config, new CustomConnection());

// Or setting it later
$ad = new Adldap($config);

$ad->setConnection(new CustomConnection());

try {
    $ad->connect();
} catch (Adldap\Exceptions\AdldapException $e) {
    // Binding failed!
}
```

## Changing Configurations

If you'd like to change configuration on the fly, use the `setConfiguration()` method:

```php
$ad = new Adldap($someOtherConfiguration);

$newConfig = new Adldap\Connections\Configuration();

$ad->setConfiguration($newConfig);
```
