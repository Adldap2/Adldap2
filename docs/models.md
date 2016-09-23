# Models

## Creating

Creating LDAP entries manually is always a pain, but Adldap2 makes it effortless. Let's get started.

When you have a provider instance, call the `make()` method. This returns an `Adldap\Models\Factory` instance:

```php
$factory = $provider->make();
```

Or you can chain all methods if you'd prefer:

```php
$user = $provider->make()->user();
```

### Available Make Methods:

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

## Saving

When you have any Adldap model instance, you can call the `save()` method to persist the
changes to your server. This method returns a `boolean`. For example:

```php
$user = $provider->make()->user([
    'cn' => 'New User',
]);

if ($user->save()) {
    // User was saved.
} else {
    // There was an issue saving this user.
}
```

The save method is actually a glorified decision maker on
whether or not to call the `create()` or `update()` methods on the model.

It merely just checks if the model exists already in your AD server:

```php
// Model.php

public function save()
{
    if ($this->exists) {
        return $this->update();
    } else {
        return $this->create();
    }
}
```

How does it know if the model exists in AD? Well, when models are constructed from AD
search results, the `exists` property on the model is set to `true`.

It's also good to know, that when a model is saved successfully (whether created or updated),
the models attributes are re-synced from your AD.

### Creating

If you are sure the model **does not exist** already inside your AD, you can use the `create()` method:

```php
$user = $provider->make()->user([
    'cn' => 'New User',
]);

if ($user->create()) {
    // User was created.
} else {
    // There was an issue creating this user.
}
```

> **Note**: When you call the create method, if the model does not have a
> distinguished name, one will automatically be generated for you using your
> `base_dn` set in your configuration and the models common name.

### Updating

If you are sure the model **does exist** already inside your AD, you can use the `update()` method:

```php
$user = $provider->search()->whereEquals('cn', 'John Doe')->firstOrFail();

$user->cn = 'Suzy Doe';

if ($user->update()) {
    // User was updated.
} else {
    // There was an issue updating this user.
}
```

## Attributes

Model attributes can be set / removed / created a couple different ways.

### Getting Attributes

All attributes can get retrieved via the `getAttribute($attribute, $subKey = 0)` method:

```php
$user->getAttribute('cn');
```

Or can be accessed dynamically as a property:

```php
$user->cn;
```

### Setting Attributes

All attributes can be set via the `setAttribute($attribute, $value, $subKey = 0)` method:

```php
$user->setAttribute('cn', 'Common Name');
```

Or, set the attribute manually:

```php
$user->cn = 'Common Name';
```

### Creating Attributes

To create an attribute that does not exist on the model, you can set it like a regular property:

```php
$user = $provider->search()->whereEquals('cn', 'John Doe')->firstOrFail();

$user->new = 'New Attribute';

$user->save();
```

If the set attribute does not exist on the model already,
it will automatically be created when you call the `save()` method.

If you'd like manually create new attributes individually, call the `createAttribute($attribute, $value)` method:

```php
if ($user->createAttribute('new', 'New Attribute')) {
    // Attribute created.
}
```

### Modifying Attributes

To modify an attribute you can either use a setter method, or by setting it manually:

> **Note**: You can also utilize setters to create new attributes if your model does not already have the attribute.

```php
$user = $provider->search()->whereEquals('cn', 'John Doe')->firstOrFail();

$user->cn = 'New Name';

// Or use a setter:

$user->setCommonName('New Name');

$user->save();
```

If you'd like to update attributes individually, call the `updateAttribute($attribute, $value)` method:

```php
if ($user->updateAttribute('cn', 'New Name')) {
    // Successfully updated attribute.
}
```

### Removing Attributes

To remove attributes, set the attribute to `NULL`:

```php
$user->cn = null;

$user->save();
```

Or, you can call the `deleteAttribute($attribute)` method:

```php
if ($user->deleteAttribute('cn')) {
    // Attribute has been deleted.
}
```

## Moving / Renaming

To move a user from one DN or OU to another, use the `move($newRdn, $newParentDn)` method:

```php
// New Relative distinguished name.
$newRdn = 'cn=John Doe';

// New parent distiguished name.
$newParentDn = 'OU=New Ou,DC=corp,DC=local';

if ($user->move($newRdn, $newParentDn) {
    // User was successfully moved to the new OU.
}
```

If you would like to keep the models old RDN along side their new RDN, pass in false in the last parameter:

```php
// New Relative distinguished name.
$newRdn = 'cn=John Doe';

// New parent distiguished name.
$newParentDn = 'OU=New Ou,DC=corp,DC=local';

if ($user->move($newRdn, $newParentDn, $deleteOldRdn = false) {
    // User was successfully moved to the new OU,
    // and their old RDN has been left in-tact.
}
```

To rename a users DN, just pass in their new relative distinguished name in the `rename($newRdn)` method:

```php
$newRdn = 'cn=New Name';

if ($user->rename($newRdn)) {
    // User was successfully renamed.
}
```

> **Note**: The `rename()` method is actually an alias for the `move()` method.

## Deleting

To delete a model, just call the `delete()` method:

```php
$user = $provider->search()->whereEquals('cn', 'John Doe')->firstOrFail();

echo $user->exists; // Returns true.

if ($user->delete()) {
    // Successfully deleted user.

    echo $user->exists; // Returns false.
}
```

## Misc

```php
// Checking if a model has an attribute.
if ($user->hasAttribute('cn')) {
    // This user has a common name.
}

// Counting the models attributes.
$count = $user->countAttributes();

// Retrieving the models modified attributes.
$attributes = $user->getDirty();

// Retrieving the users original attributes (before modifications).
$attributes = $user->getOriginal();
```


