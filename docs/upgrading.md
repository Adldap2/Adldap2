# Upgrade Guide

## Upgrading to v6 from v5.2

Due to the `v6` being a new major release, large changes have occurred. You will need to modify your code accordingly.

#### Adldap\Adldap Class

Adldap now supports multiple LDAP connections at once. The `Adldap\Adldap` class is now a "Gateway" to multiple connections.

You now construct an Adldap instance, and then attach connection providers (`Adldap\Connections\Provider`) to it.

For example:

```php
$provider = new \Adldap\Connections\Provider($config, $connection, $schema);

$ad = new Adldap();

$ad->addProvider('provider-name', $provider);

$ad->connect('provider-name');
```

#### Adldap\Schemas\ActiveDirectory

The `ActiveDirectory` schema has now been removed in favor of a Schema object.

You can utilize the schema object to manage which Schema you'd like to use, for example:

```php
$schema = new \Adldap\Schemas\ActiveDirectory();

\Adldap\Schemas\Schema::set($schema);
```

Then you can use the `\Adldap\Schemas\Schema` object to retrieve your current schema:

```php
$schema = \Adldap\Schemas\Schema::get();

$user = $provider->search()->where($schema->commonName(), '=', 'John Doe')->first();
```
