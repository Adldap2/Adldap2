# Organizational Units

### All

To retrieve all OU's on your AD server, use the `all()` method:

```php
$results = $ad->ous()->all();
````

You can also customize your results by providing some parameters inside the function like so:

```php
$fields = array('name');

$results = $ad->ous()->all($fields, $sorted = true, $sortBy = 'ou', $sortDirection = 'asc');
```

### Search

To search for only OUs, use the `search()` method:

```php
$ous = $ad->ous()->search()->whereStartsWith('ou', 'Accounts')->get();
```

### Find

To retrieve information on a specific OU, use the `find()` method:

```php
$ou = $ad->ous()->find('User Accounts');
```

You can also customize the fields that are returned by passing in field array in the second parameter:

```php
$fields = array('name', 'ou');

$computer = $ad->ous()->find('User Accounts', $fields);
```

### New Instance

To create a new Organizational Unit instance, use the `newInstance()` method:

    $attributes = [
        'ou' => 'Accounts',
    ];

    $ou = $ad->ous()->newInstance($attributes);
    
    if($ou->save())
    {
        // OU has been created!
    } else
    {
        // Looks like there was an issue saving this OU
    }

### Create

To create a group, use the `create()` method:

    $attributes = [
        'ou' => 'Accounting Department',
        'dn' => 'ou=Accounts,dc=corp,dc=Acme,dc=org',
    ];
    
    if($ad->ous()->create($attributes))
    {
        // OU has been created!
    }
