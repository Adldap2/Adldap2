## Getting Started

To connect to your Active Directory server, you'll have to provide a configuration array to a new Adldap instance.

This can be done like so:

    use Adldap\Adldap;

    $configuration = array(
        'account_suffix' => '@domain.local',
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
   
When creating a new Adldap instance, it will automatically try and connect to your server, however this behavior
is completely configurable, and you can supply your own connection class to run LDAP queries off of if you wish.
We'll discuss this later.

#### Authentication

Authenticating a user is easy, just all the `authenticate()` method like so:

    $authenticated = $ad->authenticate('johndoe', 'password');
    
    echo $authenticated; // Returns true
    
However, if you'd like to stay authenticated as this user, you'll have to pass in `true` as the third parameter like so:

    $preventRebind = true;
    
    $authenticated = $ad->authenticate('johndoe', 'password', $preventRebind);
    
