# Creating

Creating LDAP entries manually is always a pain, but Adldap2 makes it effortless. Let's get started.

When you have a provider instance, call the `make()` method. This returns an `Adldap\Models\Factory` instance:

```php
$factory = $provider->make();
```

Or you can chain all methods if you'd prefer:

```php
$user = $provider->make()->user();
```

## Available Make Methods:

When calling a make method, all of them accept an `$attributes` parameter
to fill the model with your specified attributes.

```php
// Adldap\Models\User
$user = $provider->make()->user([
    'cn' => 'John Doe',
]);

// Adldap\Models\Computer
$computer = $provider->make()->computer([
    'cn' => 'COMP-101',
]);

// Adldap\Models\Contact
$contact = $provider->make()->contact([
    'cn' => 'Suzy Doe',
]);

// Adldap\Models\Container
$container = $provider->make()->container([
    'cn' => 'VPN Users',
]);

// Adldap\Models\Group
$group = $provider->make()->group([
    'cn' => 'Managers',
]);

// Adldap\Models\OrganizationalUnit
$ou = $provider->make()->ou([
    'cn' => 'Acme',
]);
```
