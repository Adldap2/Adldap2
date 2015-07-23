## Getting Started

### Configuration

Configuring Adldap is easy. First create a Configuration object:

    $config = new \Adldap\Connections\Configuration();
    
Once you've created one, you can apply options to it through its methods.

#### Admin Username

    $config->setAdminUsername('admin');
    
#### Admin Password

    $config->setAdminPassword('correcthorsebatterystaple');

#### Domain Controllers
    
    $controllers = ['dc01.corp.company.org', 'dc02.corp.company.org'];
    
    $config->setDomainControllers($controllers);

#### Base Distinguished Name

    $config->setBaseDn('DC=corp,DC=company,DC=org');

#### Account Suffix

    $config->setAccountSuffix('@company.org');

#### Port

> Note, the port is set automatically depending on the configured
> protocol. You should only change it if your AD server has a unique port.

    $config->setPort(389);

#### Use SSL

    $config->setUseSSL(true);

#### Use TLS

    $config->setUseTLS(true);

#### Use SSO

    $config->setUseSSO(true);


### Basic Functionality

To connect to your Active Directory server, you'll have to provide a configuration instance to a new Adldap instance.

This can be done like so:

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

    $ad->getConnection()->showErrors();
    
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
