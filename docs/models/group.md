# The Group Model

## Getting a groups members

When you receive a `Group` model instance, it will contain a `member`
attribute which contains the distinguished names of all
the members inside the group.

```php
$group = $provider->search()->groups()->first();

foreach ($group->members as $member) {

    // 'cn=John Doe,dc=corp,dc=acme,dc=org'
    echo $member;

}
```

But this might not be useful, since we might actually want the models for each member.

This can be easily done with the `getMembers()` method on the group.

```php
$group = $provider->search()->groups()->first();

foreach ($group->getMembers() as $member) {

    // Will be an instance of a Adldap `Model`
    $member->getCommonName();

}
```

You should be aware however, that calling the `getMembers()` method will
query your `AD` server for **every** member contained in
the group to retrieve its model.

Think of this example below as what is being called behind the scenes:

```php
$group = $provider->search()->groups()->first();

foreach ($group->members as $member) {

    $model = $provider->search()->findByDn($member);

}
```

### Paginating Group members

The group you're looking for might contain hundreds / thousands of members.

In this case, your server might only return you a portion of the groups members.

To get around this limit, you need to ask your server to paginate the groups members through a select:

```php
$group = $provider->search()->groups()->select('member;range=0-500')->first();

foreach ($group->members as $member) {
    // We'll only have 500 members in this query.
}
```

Now, when we have the group instance, we'll only have the first `500` members inside this group. However, calling the `getMembers()` method will automatically retrieve the rest of the members for you:

```php
$group = $provider->search()->groups()->select('member;range=0-500')->first();

foreach ($group->getMembers() as $member) {
    
    // Adldap will automatically retrieve the next 500 records until it's retrieved all records.
    
    $member->getCommonName();
    
}
```
