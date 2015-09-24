## Computers Class

### All

To retrieve all computers on your AD server, use the `all()` method:

```php
$results = $ad->computers()->all();
````

You can also customize your results by providing some parameters inside the function like so:

```php
$fields = array('operatingsystem');

$results = $ad->computers()->all($fields, $sorted = true, $sortBy = 'cn');
```

### Search

To search for only computers, use the `search()` method:

```php
$computers = $ad->computers()->search()->whereStartsWith('operatingSystem', 'Windows 7')->get();
```

### Find

To retrieve information on a specific computer, use the `find()` method:

```php
$computer = $ad->computers()->find('WIN-PC');
```

You can also customize the fields that are returned by passing in field array in the second parameter:

```php
$fields = array('operatingsystem', 'operatingsystemversion');

$computer = $ad->computers()->find('WIN-PC', $fields);
```

### New Instance

To create a new Computer instance, call the `newInstance()` method:

```php
$attributes = [
    'cn' => 'COMP-101',
];

$computer = $ad->computers()->newInstance($attributes);

if($computer->save())
{
    // Computer was created!
} else 
{
    // There was an issue creating this computer
}
```

### Create

To create a Computer, call the `create()` method:

```php
$attributes = [
    'cn' => 'COMP-101',
    'dn' => 'cn=COMP-101,dc=corp,dc=acme,dc=org',
];

if($ad->computers()->create($attributes))
{
    // Computer was created!
}
```
