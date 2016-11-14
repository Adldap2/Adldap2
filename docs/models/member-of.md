# Member Of Methods

The following models utilize the trait `HasMemberOf`, which contains methods that utilize the `memberof` attribute.

- [Group](docs/group.md)
- [User](docs/user.md)

## Retrieving the models groups

To get the groups a model is apart of, call the `getGroups()` method:

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

## Adding a Group

To add the model to a Group, use the method `addGroup()`:

```php
$group = $provider->search()->groups()->find('Accounting');

$user = $provider->search()->groups()->find('jdoe');

// You can either pass a Group instance:
$user->addGroup($group);

// Or pass the Group's DN:
$user->addGroup('cn=Accounting,dc=corp,dc=acme,dc=org');
```

## Removing a Group

To remove the model from a Group, use the method `removeGroup()`:

```php
$user = $provider->search()->groups()->find('jdoe');

$group = $user->getGroups()->first();

// You can either pass a Group instance:
$user->removeGroup($group);

// Or pass the Group's DN:
$user->removeGroup('cn=Accounting,dc=corp,dc=acme,dc=org');
```
