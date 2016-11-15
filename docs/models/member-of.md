# Member Of Methods

The following models utilize the trait `Adldap\Models\Traits\HasMemberOfTrait`, which contains methods that utilize the `memberof` LDAP attribute.

- [Group](docs/group.md)
- [User](docs/user.md)

## Getting the models groups

To retrieve groups a model is apart of, call the `getGroups()` method:

```php
$user = $provider->search()->users()->find('jdoe');

$groups = $user->getGroups();

foreach ($groups as $group) {

    // Instances of the `Group` model will be given:
    $group->getMembers();

}
```

However, calling the `getGroups()` method only returns the **first** level of groups that the model is apart of.

To recursively retrieve groups of groups that the model is apart of, pass in `true` for the first parameter:

```php
// Now groups of groups will be returned in a single dimensional collection.

$groups = $user->getGroups($recursive = true);
```

## Getting only group names

To retrieve the group names the model is apart of, call the `getGroupNames()` method:

> **Note**: Like the `getGroups()` method, it accepts a `$recursive` argument to retrieve
> nested group names. Keep in mind, groups that contain the same name will
> only appear once in the resulting array.

```php
$names = $user->getGroupNames();

foreach ($names as $name) {

    // Returns 'Accounting'
    echo $name;

}

// Or:

$names = $user->getGroupNames($recursive = true);
```

## Adding a Group

To add the model to a Group, use the method `addGroup()`:

```php
$group = $provider->search()->groups()->find('Accounting');

$user = $provider->search()->users()->find('jdoe');

// You can either pass a Group instance:
$user->addGroup($group);

// Or pass the Group's DN:
$user->addGroup('cn=Accounting,dc=corp,dc=acme,dc=org');
```

## Removing a Group

To remove the model from a Group, use the method `removeGroup()`:

```php
$user = $provider->search()->users()->find('jdoe');

$group = $user->getGroups()->first();

// You can either pass a Group instance:
$user->removeGroup($group);

// Or pass the Group's DN:
$user->removeGroup('cn=Accounting,dc=corp,dc=acme,dc=org');
```

## Checking if a model is apart of a group

To check if the model is apart of a specific group, call the `inGroup()` method:

```php
$user = $provider->search()->groups()->find('jdoe');

$office = $provider->search()->groups()->find('Office');

// You can either supply the Groups DN or a group model:

if ($user->inGroup($office)) {

    // This user is apart of the office group!

}

if ($user->inGroup('cn=Office,dc=corp,dc=acme,dc=org')) {

    //

}
```

However, much like the above methods for retrieving nested groups,
you'll need to pass an extra parameter indicating you
would like to check nested groups:

```php
$user = $provider->search()->groups()->find('jdoe');

$office = $provider->search()->groups()->find('Office');

if ($user->inGroup($office, $recursive = true)) {

    // This user might apart of the `Office` group by inheritance.

}
```

You can also check for the existence of multiple groups by providing an
array. You can mix and match whether you provide a
Group model or a Groups DN:

```php
$user = $provider->search()->groups()->find('jdoe');

$office = $provider->search()->groups()->find('Office');

$groups = [
    $office,
    'cn=Human Resources,dc=corp,dc=acme,dc=org',
];

if ($user->inGroup($groups, $recursive = true)) {
    
    // This user is apart of **both** the `Office` and `Human Resources` group!

}
```
