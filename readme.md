<h1 align="center">Adldap2</h1>

<p align="center">
<a href="https://travis-ci.org/Adldap2/Adldap2"><img src="https://img.shields.io/travis/Adldap2/Adldap2.svg?style=flat-square"/></a>
<a href="https://scrutinizer-ci.com/g/Adldap2/Adldap2/?branch=master"><img src="https://img.shields.io/scrutinizer/g/adLDAP2/adLDAP2/master.svg?style=flat-square"/></a>
<a href="https://packagist.org/packages/adldap2/adldap2"><img src="https://img.shields.io/packagist/dt/adldap2/adldap2.svg?style=flat-square"/></a>
<a href="https://packagist.org/packages/adldap2/adldap2"><img src="https://img.shields.io/packagist/v/adldap2/adldap2.svg?style=flat-square"/></a>
<a href="https://packagist.org/packages/adldap2/adldap2"><img src="https://img.shields.io/packagist/l/adldap2/adldap2.svg?style=flat-square"/></a>
</p>

<p align="center">
Working with LDAP doesn't need to be hard.
</p>

<p align="center">
Adldap2 is a PHP package that provides LDAP authentication and directory management tools using the Active Record pattern.
</p>

---

## Index

- [Introduction](docs/introduction.md)
- [Installation](docs/installation.md)
- [Setup](docs/setup.md)
- [Searching](docs/searching.md)
- [Creating / Updating](docs/models/model.md)
  - [Computer](docs/models/computer.md)
  - [Contact](docs/models/contact.md)
  - [Container](docs/models/container.md)
  - [Entry (Base Model)](docs/models/entry.md)
  - [Group](docs/models/group.md)
  - [Organizational Unit](docs/models/ou.md)
  - [Printer](docs/models/printer.md)
  - [RootDse](docs/models/root-dse.md)
  - [User](docs/models/user.md)
- [Working With Distiguished Names](docs/distinguished-names.md)
- [Troubleshooting](docs/troubleshooting.md)

## Implementations

- [Laravel](https://github.com/Adldap2/Adldap2-Laravel)
- [Kohana](https://github.com/Adldap2/Adldap2-Kohana)

## Quick Start

```php
// Construct new Adldap instance.
$ad = new \Adldap\Adldap();

// Create a configuration array.
$config = [  
  // An array of your LDAP hosts. You can use either
  // the host name or the IP address of your host.
  'hosts'    => ['ACME-DC01.corp.acme.org', '192.168.1.1'],

  // The base distinguished name of your domain to perform searches upon.
  'base_dn'  => 'dc=corp,dc=acme,dc=org',

  // The account to use for querying / modifying LDAP records. This
  // does not need to be an admin account. This can also
  // be a full distinguished name of the user account.
  'username' => 'admin@corp.acme.org',
  'password' => 'password',
];

// Add a connection provider to Adldap.
$ad->addProvider($config);

try {
    // If a successful connection is made to your server, the provider will be returned.
    $provider = $ad->connect();

    // Performing a query.
    $results = $provider->search()->where('cn', '=', 'John Doe')->get();

    // Finding a record.
    $user = $provider->search()->find('jdoe');

    // Creating a new LDAP entry. You can pass in attributes into the make methods.
    $user =  $provider->make()->user([
        'cn'          => 'John Doe',
        'title'       => 'Accountant',
        'description' => 'User Account',
    ]);

    // Setting a model's attribute.
    $user->cn = 'John Doe';

    // Saving the changes to your LDAP server.
    if ($user->save()) {
        // User was saved!
    }
} catch (\Adldap\Auth\BindException $e) {

    // There was an issue binding / connecting to the server.

}
```

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
