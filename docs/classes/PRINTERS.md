## Printers Class

### All

To retrieve all printers on your AD server, use the `all()` method:

```php
$results = $ad->printers()->all();
````

You can also customize your results by providing some parameters inside the function like so:

```php
$fields = array('printsharename');

$results = $ad->computers()->all($fields, $sorted = true, $sortBy = 'printsharename');
```

### Search

To search for only printers, use the `search()` method:

```php
$printers = $ad->printers()->search()->whereStartsWith('printsharename', 'ACCOUNTING-HP')->get();
```

### Find

To retrieve information on a specific printer, use the `find()` method:

```php
$computer = $ad->printers()->find('ACCOUNTING-HP');
```
