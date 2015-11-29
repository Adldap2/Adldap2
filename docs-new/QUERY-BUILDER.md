# Query Builder

The Adldap2 query builder makes building LDAP queries feel effortless. Let's get started.

## Selects

#### Selecting attributes

Selecting certain LDAP attributes will increase the speed of your queries.

```php
// Passing in an array of attributes
$search->select(['cn', 'samaccountname', 'telephone', 'mail']);

// Passing in each attribute as an argument
$search->select('cn', 'samaccountname', 'telephone', 'mail');
```

#### Finding a specific record

If you're trying to find a single record, but not sure what the record might be, use the `find()` method:

```php
$record = $search->find('John Doe');

if($record)
{
    // Record was found!    
} else
{
    // Hmm, looks like we couldn't find anything...
}
```

> **Note**: Using the `find()` method will search for LDAP records using ANR (ambiguous name resolution).
> For a more fine-tuned search, use the `findBy()` method below.

##### Finding a specific record (or failing)

If you'd like to try and find a single record and throw an exception when it hasn't been
found, use the `findOrFail()` method:

```php
try
{
    $record = $search->findOrFail('John Doe');
} catch (Adldap\Exceptions\ModelNotFoundException $e)
{
    // Record wasn't found!
}
```

#### Finding a specific record by a specific attribute

If you're looking for a single record with a specific attribute, use the `findBy()` method:

```php
// We're looking for a record with the 'samaccountname' of 'jdoe'.
$record = $search->findBy('samaccountname', 'jdoe');
```

##### Finding a specific record by a specific attribute (or failing)

If you'd like to try and find a single record by a specific attribute and throw
an exception when it hasn't been found, use the `findByOrFail()` method:

```php
try
{
    $record = $search->findByOrFail('samaccountname', 'jdoe');
} catch (Adldap\Exceptions\ModelNotFoundException $e)
{
    // Record wasn't found!
}
```

#### Retrieving results

To get the results from a search, simply call the `get()` method:

```php
$results = $search->select(['cn', 'samaccountname'])->get();
```

##### Retrieving all LDAP records

To get all records from LDAP, call the `all()` method:

```php
$results = $search->all();
```

##### Retrieving the first record

To retrieve the first record of a search, call the `first()` method:

```php
$record = $search->first();
```

###### Retrieving the first record (or failing)

To retrieve the first record of a search or throw an exception when one isn't found, call the `firstOrFail()` method:

```php
try {
    $record = $search->first();
} catch (Adldap\Exceptions\ModelNotFoundException $e) {
    // Record wasn't found!
}
```

## Wheres

To perform a where clause on the search object, use the `where()` function:

```php
$search->where('cn', '=', 'John Doe');
```

This query would look for a record with the common name of 'John Doe' and return the results.

#### Where Starts With

We could also perform a search for all objects beginning with the common name of 'John' using the `starts_with` operator:

```php
$results = $ad->search()->where('cn', 'starts_with', 'John')->get();

// Or use the method whereStartsWith($attribute, $value)

$results = $ad->search()->whereStartsWith('cn', 'John')->get();
```

#### Where Ends With
    
We can also search for all objects that end with the common name of `Doe` using the `ends_with` operator:

```php
$results = $ad->search()->where('cn', 'ends_with', 'Doe')->get();

// Or use the method whereEndsWith($attribute, $value)

$results = $ad->search()->whereEndsWith('cn', 'Doe')->get();
```

#### Where Contains

We can also search for all objects with a common name that contains `John Doe` using the `contains` operator:

```php
$results = $ad->search()->where('cn', 'contains', 'John Doe')->get();

// Or use the method whereContains($attribute, $value)

$results = $ad->search()->whereContains($attribute, $value)->get();
```

#### Where Has

Or we can retrieve all objects that have a common name attribute using the wildcard operator (`*`):

```php
$results = $ad->search()->where('cn', '*')->get();

// Or use the method whereHas($field)
$results = $ad->search()->whereHas('cn')->get();
```

This type of filter syntax allows you to clearly see what your searching for.

Remember, fields are case insensitive, so it doesn't matter if you use `->where('CN', '*')` or `->where('cn', '*')`,
they would return the same result.
   
It's also good to know that all values inserted into a where, or an orWhere method,
<b>are escaped</b> by default into a hex string, so you don't need to worry about escaping them. For example:

```php
// Returns '(cn=\74\65\73\74\2f\2f\75\6e\2d\65\73\63\61\70\69\6e\67\2f\2f)'
$query = $ad->search()->where('cn', '=', 'test//un-escaping//')->getQuery();
```

## Or Wheres

To perform an 'or where' clause on the search object, use the `orWhere()` function. However, please be aware this
function performs differently than it would on a database. For example:

```php
$results = $search
            ->where('cn', '=', 'John Doe')
            ->orWhere('cn' '=', 'Suzy Doe')
            ->get();
```
    
This query would return no results, because we're already defining that the common name (`cn`) must equal `John Doe`. Applying
the `orWhere()` does not amount to 'Look for an object with the common name as "John Doe" OR "Suzy Doe"'. This query would
actually amount to 'Look for an object with the common name that <b>equals</b> "John Doe" OR "Suzy Doe"

To solve the above problem, we would use `orWhere()` for both fields. For example:

```php
$results = $search
        ->orWhere('cn', '=', 'John Doe')
        ->orWhere('cn' '=', 'Suzy Doe')
        ->get();
```

Now, we'll retrieve both John and Suzy's AD records, because the common name can equal either.

> *Note*: You can also use all `where` methods as an or where, for example:
`orWhereHas()`, `orWhereContains()`, `orWhereStartsWith()`, `orWhereEndsWith()`

## Raw Filters

Sometimes you might just want to add a raw filter without using the query builder.
You can do so by using the `rawFilter()` method:

```php
$filter = '(samaccountname=jdoe)';

$results = $search->rawFilter($filter)->get();
```

