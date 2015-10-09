# Getting Started

## Authenticating Users

To authenticate a user against your a AD server, use the `authenticate()` method on the Adldap instance:

    $ad = new Adldap($config);
    
    if($ad->authenticate($username, $password)
    {
        // User passed authentication
    } else
    {
        // Uh oh, looks like the username or password is incorrect
    }

If you'd like to *bind* a user to your AD server so all operations ran through Adldap are run as the authenticated user,
pass in `true` into the third parameter:

    if($ad->authenticate($username, $password, true)
    {
        // User passed authentication
    } else
    {
        // Uh oh, looks like the username or password is incorrect
    }

> **Note**: Keep in mind if you authenticate but not bind the user to your server, all operations will be
ran under the configurations administrator credentials.

## Connecting Manually

If you'd like to connect manually instead of connecting immediately when Adldap is constructed, pass in `false` in the third paramter:

    $ad = new Adldap($config, null, $autoConnect = false);
    
    // Then call
    
    $ad->connect();

## Changing Connections

If you'd like to create your own custom Connection class, use the `setConnection()` method or insert it into the
second parameter when you're constructing a new Adldap instance:
    
    // CustomConnection.php
    class CustomConnection extends Adldap\Connections\Ldap
    {
        
    }

Then in some other file:

    // Inserting in the second parameter
    $ad = new Adldap($config, new CustomConnection());
    
    // Or setting auto-connect to false and setting the connection later
    $ad = new Adldap($config, null, $autoConnect = false);
    
    $ad->setConnection(new CustomConnection());
    
    $ad->connect();

## Changing Configurations

If you'd like to change configuration on the fly, use the `setConfiguration()` method:

    $ad = new Adldap($someOtherConfiguration);

    $newConfig = new Adldap\Connections\Configuration();
    
    $ad->setConfiguration($newConfig);


    
