# Configuration

Configuring Adldap2 is really easy. Let's get started.

## Using a configuration array

You can configure Adldap2 by supplying an array. Keep in mind not all of these are required. This will be discussed below.
Here is an example array with all possible configuration options:

```php
// Create the configuration array.
$config = [
    // Mandatory Configuration Options
    'domain_controllers'    => ['corp-dc1.corp.acme.org', 'corp-dc2.corp.acme.org'],
    'base_dn'               => 'dc=corp,dc=acme,dc=org',
    'admin_username'        => 'admin',
    'admin_password'        => 'password',
    
    // Optional Configuration Options
    'account_prefix'        => 'ACME-',
    'account_suffix'        => '@acme.org',
    'admin_account_suffix'  => '@acme.org',
    'port'                  => 389,
    'follow_referrals'      => false,
    'use_ssl'               => false,
    'use_tls'               => false,
    'timeout'               => 5,
];

// Create a new Adldap Provider instance.
$provider = new \Adldap\Connections\Provider($config);
```

## Using an configuration object

You can configure Adldap in an object oriented way by creating a `Configuration` object. Keep in mind, not all of these
methods are required. This will be discussed below. Here is an example of a Configuration with all possible configuration options.

```php
// Create a new Configuration object.
$config = new \Adldap\Connections\Configuration();

// Mandatory Configuration Options
$config->setDomainControllers(['corp-dc1.corp.acme.org', 'corp-dc2.corp.acme.org']);
$config->setBaseDn('dc=corp,dc=acme,dc=org');
$config->setAdminUsername('admin');
$config->setAdminPassword('password');

// Optional Configuration Options
$config->setAccountPrefix('ACME-');
$config->setAccountSuffix('@acme.org');
$config->setAdminAccountSuffix('@acme.org');
$config->setPort(389);
$config->setFollowReferrals(false);
$config->setUseSSL(false);
$config->setUseTLS(false);
$config->setTimeout(5);

// Create a new Adldap Provider instance.
$provider = new \Adldap\Connections\Provider($config);
```

## Definitions

### Account Prefix (optional)

The account prefix option is the prefix of your user accounts in AD. This is usually not needed (if utilizing the
account suffix), however the functionality is in place if you would like to only allow certain users with
the specified prefix to login, or add a domain so you're users do not have to specify one.

### Account Suffix (optional)

The account suffix option is the suffix of your user accounts in AD. For example, if your domain DN is `DC=corp,DC=acme,DC=org`,
then your account suffix would be `@corp.acme.org`. This is then appended to then end of your user accounts on authentication.

For example, if you're binding as a user, and your username is `jdoe`, then Adldap would try to authenticate with
your server as `jdoe@corp.acme.org`.

### Admin Account Suffix (optional)

The admin account suffix option is the suffix of your administrator account in AD. Having a separate suffix for user accounts
and administrator accounts allows you to bind your admin under a different suffix than user accounts.

### Domain Controllers (required)

The domain controllers option is an array of servers located on your network that serve Active Directory. You insert as many
servers or as little as you'd like depending on your forest (with the minimum of one of course).

For example, if the server name that hosts AD on my network is named `ACME-DC01`, then I would insert `['ACME-DC01.corp.acme.org']`
inside the domain controllers option array.

### Port (optional)

The port option is used for authenticating and binding to your AD server. The default ports are already used for non SSL and SSL connections (389 and 636).

Only insert a port if your AD server uses a unique port.

### Base Distinguished Name (optional)

The base distinguished name is the base distinguished name you'd like to perform operations on. An example base DN would be `DC=corp,DC=acme,DC=org`.

If one is not defined, then Adldap will try to find it automatically by querying your server. It's recommended to include it to limit queries executed per request.

### Administrator Username & Password (required)

When connecting to your AD server, an administrator username and password is required to be able to query and run operations on your server(s).
You can use any user account that has these permissions.

### Follow Referrals (optional)

The follow referrals option is a boolean to tell active directory to follow a referral to another server on your network if the
server queried knows the information your asking for exists, but does not yet contain a copy of it locally. This option is defaulted to false.

For more information, visit: https://technet.microsoft.com/en-us/library/cc978014.aspx

### SSL & TLS (optional)

If you need to be able to change user passwords on your server, then an SSL *or* TLS connection is required. All other operations
are allowed on unsecured protocols. These options are definitely recommended if you have the ability to connect to your server
securely.

### Timeout

The timeout option allows you to configure the amount of seconds to wait until your application receives a response from your LDAP server.

The default is 5 seconds.
