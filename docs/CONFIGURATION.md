# Configuration

Configuring Adldap is really easy. Let's get started.

### Using a configuration array

You can configure Adldap by supplying an array. Keep in mind not all of these are required. This will be discussed below. Here is an example array with all possible configuration options:

    $config = [
        'account_suffix' => '@acme.org',
        'domain_controllers' => ['corp-dc1.corp.acme.org', 'corp-dc2.corp.acme.org'],
        'port' => 389,
        'base_dn' => 'dc=corp,dc=acme,dc=org',
        'admin_username' => 'username',
        'admin_password' => 'password',
        'follow_referalls' => 1,
        'use_ssl' => false,
        'use_tls' => false,
        'use_sso' => false,
    ];
    
    $ad = new \Adldap\Adldap($config);

### Using an configuration object

You can configure Adldap in an object oriented way by creating a `Configuration` object. Keep in mind, not all of these methods are required. This will be discussed below. Here is an example of a Configuration with all possible configuration options.

    $config = new \Adldap\Connections\Configuration();
    
    $config->setAccountSuffix('@acme.org');
    $config->setDomainControllers(['corp-dc1.corp.acme.org', 'corp-dc2.corp.acme.org']);
    $config->setPort(389);
    $config->setBaseDn('dc=corp,dc=acme,dc=org');
    $config->setAdminUsername('username');
    $config->setAdminPassword('password');
    $config->setFollowReferrals(1);
    $config->setUseSSL(false);
    $config->setUseTLS(false);
    $config->setUseSSO(false);
    
    $ad = new \Adldap\Adldap($config);
    
