# HasMemberOfTrait

Models that contain this trait, have the ability to be apart of a group.

There's many helpful methods to assist you in all of the operations related to group membership, let's get started!

## Retrieving Groups

To retrieve the groups that a model is apart of, call the `getGroups()` method:

```php
$user = $provider->search()->users()->find('jdoe');

$groups = $user->getGroups();

foreach ($groups as $group) {

    $group->getCommonName(); // ex. 'Accounting'

}
```

We can also pass in specific fields we need from the returned groups to speed up our queries.

For example, if we only need the groups common name:

```php
// Group models will be returned with only their common name.
$groups = $user->getGroups(['cn']);
```

However, calling `getGroups()` will only retrieve the models immediate groups (non-recursive).

To retrieve nested groups, pass in `true` into the second parameter:

```php
$groups = $user->getGroups([], $recursive = true);
```

## Retrieve Group Names

If you only want the models group names, call the `getGroupNames()` method:

```php
$names = $user->getGroupNames();

foreach ($names as $name) {

    echo $name; // ex. 'Accounting'

}
```

However, this method will also retrieve only the immediate groups names
much like the `getGroups()` method. You'll need to pass in `true` in
the first parameter to retrieve results recursively.

```php
$names = $user->getGroupNames($recursive = true);
```

## Adding a Group

To add the model to a specific group, call the `addGroup()` method:

```php
$group = $provider->search()->groups()->find('Accounting');

// You can either provide a Group model:
if ($user->addGroup($group)) {

    //

}

// Or a Groups DN:
if ($user->addGroup('cn=Accounting,ou=Groups,dc=acme,dc=org')) {

    //

}
```

> **Note**: You do not need to call the `save()` method for adding / removing groups.
> This is done automatically so you can perform clean `if` statements on the method.

## Removing a Group

To remove the model from a specific group, call the `removeGroup()` method:

```php
$group = $user->getGroups()->first();

// You can either provide a Group model:
if ($user->removeGroup($group)) {

    //

}

// Or the groups DN:
if ($user->removeGroup('cn=Accounting,ou=Office Groups,dc=acme,dc=org')) {

    //

}
```
