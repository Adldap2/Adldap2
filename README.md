# Adldap2

[![Build Status](https://img.shields.io/travis/Adldap2/Adldap2.svg?style=flat-square)](https://travis-ci.org/Adldap2/Adldap2)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/adLDAP2/adLDAP2/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/adLDAP2/adLDAP2/?branch=master)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/45a86fc2-b202-4f1b-9549-679900e5807c.svg?style=flat-square)](https://insight.sensiolabs.com/projects/45a86fc2-b202-4f1b-9549-679900e5807c)
[![Total Downloads](https://img.shields.io/packagist/dt/adldap2/adldap2.svg?style=flat-square)](https://packagist.org/packages/adldap2/adldap2)
[![Latest Stable Version](https://img.shields.io/packagist/v/adldap2/adldap2.svg?style=flat-square)](https://packagist.org/packages/adldap2/adldap2)
[![License](https://img.shields.io/packagist/l/adldap2/adldap2.svg?style=flat-square)](https://packagist.org/packages/adldap2/adldap2)

> Originally written by Scott Barnett and Richard Hyland. Adopted by the community.

## Description

Working with Active Directory doesn't need to be hard. Adldap2 is a tested PHP package that provides LDAP
authentication and Active Directory management tools using the Active Record pattern.

## Installation

### Requirements

To use Adldap2, your sever must support:

- PHP 5.5.9 or greater
- PHP LDAP Extension

### Optional Requirements

> **Note: Adldap makes use of `ldap_modify_batch()` for processing modifications to models**. Your server
must be on **PHP >= 5.5.10 || >= 5.6.0** to make modifications.

If your AD server requires SSL, your server must support the following libraries:

- PHP SSL Libraries (http://php.net/openssl)

### Installing

Adldap2 utilizes composer for installation. Insert `"adldap2/adldap2": "6.0.*"` in your `composer.json` file:

```json
"require": {
    "adldap2/adldap2": "6.0.*"
},
```

Then run the `composer update` command in the root of your project.

### Configuration

Configuring Adldap2 is really easy. Let's get started.

#### Using a configuration array

You can configure Adldap2 by supplying an array. Keep in mind not all of these are required. This will be discussed below.
Here is an example array with all possible configuration options:

```php
// Create the configuration array.
$config = [
    'account_suffix'        => '@acme.org',
    'domain_controllers'    => ['corp-dc1.corp.acme.org', 'corp-dc2.corp.acme.org'],
    'port'                  => 389,
    'base_dn'               => 'dc=corp,dc=acme,dc=org',
    'admin_username'        => 'username',
    'admin_password'        => 'password',
    'admin_account_suffix'  => '@acme.org',
    'follow_referrals'      => true,
    'use_ssl'               => false,
    'use_tls'               => false,
];

// Create a new Adldap Provider instance.
$provider = new \Adldap\Connections\Provider($config);
```

#### Using an configuration object

You can configure Adldap in an object oriented way by creating a `Configuration` object. Keep in mind, not all of these
methods are required. This will be discussed below. Here is an example of a Configuration with all possible configuration options.

```php
// Create a new Configuration object.
$config = new \Adldap\Connections\Configuration();

$config->setAccountSuffix('@acme.org');
$config->setDomainControllers(['corp-dc1.corp.acme.org', 'corp-dc2.corp.acme.org']);
$config->setPort(389);
$config->setBaseDn('dc=corp,dc=acme,dc=org');
$config->setAdminUsername('username');
$config->setAdminPassword('password');
$config->setFollowReferrals(false);
$config->setUseSSL(false);
$config->setUseTLS(false);

// Create a new Adldap Provider instance.
$provider = new \Adldap\Connections\Provider($config);
```
  
#### Option Definitions

##### Account Suffix (optional)

The account suffix option is the suffix of your user accounts in AD. For example, if your domain DN is `DC=corp,DC=acme,DC=org`,
then your account suffix would be `@corp.acme.org`. This is then appended to then end of your user accounts on authentication.

For example, if you're binding as a user, and your username is `jdoe`, then Adldap would try to authenticate with
your server as `jdoe@corp.acme.org`.

##### Admin Account Suffix (optional)

The admin account suffix option is the suffix of your administrator account in AD. Having a separate suffix for user accounts
and administrator accounts allows you to bind your admin under a different suffix than user accounts.

##### Domain Controllers (required)

The domain controllers option is an array of servers located on your network that serve Active Directory. You insert as many
servers or as little as you'd like depending on your forest (with the minimum of one of course).

For example, if the server name that hosts AD on my network is named `ACME-DC01`, then I would insert `['ACME-DC01.corp.acme.org']`
inside the domain controllers option array.

##### Port (optional)

The port option is used for authenticating and binding to your AD server. The default ports are already used for non SSL and SSL connections (389 and 636).

Only insert a port if your AD server uses a unique port.

##### Base Distinguished Name (optional)

The base distinguished name is the base distinguished name you'd like to perform operations on. An example base DN would be `DC=corp,DC=acme,DC=org`.

If one is not defined, then Adldap will try to find it automatically by querying your server. It's recommended to include it to limit queries executed per request.

##### Administrator Username & Password (required)

When connecting to your AD server, an administrator username and password is required to be able to query and run operations on your server(s).
You can use any user account that has these permissions.

##### Follow Referrals (optional)

The follow referrals option is a boolean to tell active directory to follow a referral to another server on your network if the
server queried knows the information your asking for exists, but does not yet contain a copy of it locally. This option is defaulted to false.

For more information, visit: https://technet.microsoft.com/en-us/library/cc978014.aspx

##### SSL & TLS (optional)

If you need to be able to change user passwords on your server, then an SSL *or* TLS connection is required. All other operations
are allowed on unsecured protocols. These options are definitely recommended if you have the ability to connect to your server
securely.

## Implementations

- [Laravel](https://github.com/Adldap2/Adldap2-Laravel)
- [Kohana](https://github.com/Adldap2/Adldap2-Kohana)

## Versioning

Adldap2 is versioned under the [Semantic Versioning](http://semver.org/) guidelines as much as possible.

Releases will be numbered with the following format:

`<major>.<minor>.<patch>`

And constructed with the following guidelines:

* Breaking backward compatibility bumps the major and resets the minor and patch.
* New additions without breaking backward compatibility bumps the minor and resets the patch.
* Bug fixes and misc changes bumps the patch.

Minor versions are not maintained individually, and you're encouraged to upgrade through to the next minor version.

Major versions are maintained individually through separate branches.

## Quick Start & and Testing an LDAP connection

If you need to test something with access to an LDAP server, the generous folks at [Georgia Tech](http://drupal.gatech.edu/handbook/public-ldap-server) have you covered.

Use the following configuration:

```php
$config = [
    'account_suffix'        => '@gatech.edu',
    'domain_controllers'    => ['whitepages.gatech.edu'],
    'base_dn'               => 'dc=whitepages,dc=gatech,dc=edu',
    'admin_username'        => '',
    'admin_password'        => '',
];

// Create a new connection provider.
$provider = new \Adldap\Connections\Provider($config);

$ad = new \Adldap\Adldap();

// Add the provider to Adldap.
$ad->addProvider('default', $provider);

// Try connecting to the provider.
// If the connection is successful, the connected provider is returned.
$provider = $ad->connect('default');

// Create a new search.
$search = $provider->search();

// Call query methods upon the search itself.
$results = $search->where('...')->get();

// Or create a new query object.
$query = $search->newQuery();

$results = $search->where('...')->get();
```

However while useful for basic testing, the queryable data only includes user data, so if you're looking for testing with any other information
or functionality such as modification, you'll have to use your own server.
