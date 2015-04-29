## Getting Started

### Basic Functionality

To connect to your Active Directory server, you'll have to provide a configuration array to a new Adldap instance.

This can be done like so:

    use Adldap\Adldap;

    $configuration = array(
        'user_id_key' => 'samaccountname',
        'account_suffix' => '@domain.local',
        'person_filter' => array('category' => 'objectCategory', 'person' => 'person'),
        'base_dn' => 'DC=domain,DC=local',
        'domain_controllers' => array('DC1.domain.local', 'DC2.domain.local'),
        'admin_username' => 'administrator',
        'admin_password' => 'password',
        'real_primarygroup' => true,
        'use_ssl' => false,
        'use_tls' => false,
        'recursive_groups' => true,
        'ad_port' => '389',
        'sso' => false,
    );
    
    try
    {
        $ad = new Adldap($configuration);
        
        echo "Awesome, we're connected!";
    } catch(AdldapException $e)
    {
        echo "Uh oh, looks like we had an issue trying to connect: $e";
    }
    
If you'd like to know what each configuration option means, please look at the [configuration page](CONFIGURATION.md).

When creating a new Adldap instance, it will automatically try and connect to your server, however this behavior
is completely configurable, and you can supply your own connection class to run LDAP queries off of if you wish.
See [Advanced Usage](#advanced-usage).

#### Authentication

Authenticating a user is easy, just call the `authenticate()` method like so:

    $authenticated = $ad->authenticate('johndoe', 'password');
    
    echo $authenticated; // Returns true
    
However, if you'd like to stay authenticated as this user, you'll have to pass in `true` as the third parameter like so:

    $preventRebind = true;
    
    $authenticated = $ad->authenticate('johndoe', 'password', $preventRebind);
    
Now, when you call methods on the Adldap object, you're authenticated as John Doe, instead of the administrator.

#### Retrieving the last message / error from LDAP:

To retrieve the last message or error from LDAP, call the `getLastError()` method like so:

    echo $ad->getLastError();
    
### Advanced Usage

#### Using a different LDAP Connection

If you'd like to supply your own LDAP connection class, supply your own by inserting it into the second parameter in
the Adldap constructor. The custom class will need to implement the `Adldap\Interfaces\ConnectionInterface` OR extend
the main Connection class (`Adldap\Connections\Ldap`).

    use Adldap\Connections\Ldap;
    
    class CustomLdapConnection extends Ldap
    {
        public function connect($hostname, $port = '389')
        {
            // Connect to LDAP my own way
        }
    }

Then using your new class:

    $connection = new CustomLdapConnection;
    
    $ad = new Adldap($configuration, $connection);

#### Disabling auto-connect on construct

By default, when Adldap is constructed, it automatically tries to connect to your server, though you can disable this
by passing in `false` in the last construct parameter. You will have to manually call the connect method by doing this.

    $ad = new Adldap($configuration, null, false);
    
    $ad->connect();
    
#### Showing LDAP Warning / Errors

By default, LDAP warnings and errors are suppressed in favor of catchable exceptions thrown by Adldap. To display
warnings and errors, use the `showErrors()` method on the connection:

    $ad->getLdapConnection()->showErrors();
    
    // Now all Adldap methods will display LDAP warnings / errors if they are thrown
    $ad->user()->all();

#### Overriding Adldap Classes and Methods

To override classes / methods, simply create a class that extends Adldap, and override them:

    use Adldap\Adldap;
    
    class MyAdldap extends Adldap {
        
        // Overriding the user function to return your own User class
        public function user()
        {
            return new MyUserClass();
        }
    
    }
