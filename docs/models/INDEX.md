# Models

When receiving search results from Adldap, you will always have an array of models depending on the type of AD entry.

## Types of Models

> Note: All models are located in the namespace `Adldap\Models\`.

- [User](https://github.com/Adldap2/Adldap2/blob/master/docs/models/USER.md)
- [Group](https://github.com/Adldap2/Adldap2/blob/master/docs/models/GROUP.md)
- [Organizational Unit](https://github.com/Adldap2/Adldap2/blob/master/docs/models/OU.md)
- [Container](https://github.com/Adldap2/Adldap2/blob/master/docs/models/CONTAINER.md)
- [Computer](https://github.com/Adldap2/Adldap2/blob/master/docs/models/COMPUTER.md)
- [Printer](https://github.com/Adldap2/Adldap2/blob/master/docs/models/PRINTER.md)
- [Exchange Server](https://github.com/Adldap2/Adldap2/blob/master/docs/models/EXCHANGE-SERVER.md)
- [Entry](https://github.com/Adldap2/Adldap2/blob/master/docs/models/ENTRY.md)

### Modifying Models

To modify any attributes on the model, you can just set the attributes manually, or use their [setters](#specific-methods-available-on-all-models):

```php
// Using a setter
$user->setCommonName('Doe, John');
$user->setFirstName('John');
$user->setLastName('Doe');

// Manually modifying, using their AD attributes
$user->cn = 'Doe, John';
$user->givenName = 'John';
$user->surname = 'Doe';
```

Adldap is smart enough to know that if an attribute doesn't exist, then it will add it to the entry.

### Saving / Updating

When you've made your modifications, just call the `save()` method. It will return true or false if the record has been
saved successfully:

```php
$user->setFirstName('First Name');
    
if($user->save())
{
    // Successfully updated record
} else
{
    // There was an issue updating this record
}
```

### Deleting

To delete a model, use the `delete()` method:

```php
if($user->delete())
{
    // Model has been successfully deleted
}
```

### Manually Creating New Attributes

If you'd like to manually create a new attribute on an existing record, use the `createAttribute()` method:

```php
$groups = [
    'CN=Accounting,OU=Groups,DC=corp,DC=acme,DC=org',
];

if($user->createAttribute('memberOf', $groups))
{
    // Successfully created attribute
}
```

### Manually Updating Attributes

If you'd like to manually update attributes on an exisiting record, use the `updateAttribute()` method:

```php
if($user->updateAttribute('cn', 'John Doe'))
{
    // Successfully updated attribute
    echo $user->cn; // John Doe
}
```

### Manually Deleting Attributes

To manually remove / delete attributes, use the `deleteAttribute()` method:

```php
if($user->deleteAttribute('memberOf'))
{
    // Successfully removed attribute
}
```

### Manually Creating a new Model Instance

To manually create a new model instance, you need to inject a new Query Builder instance into the Models second parameter:

```php
$adldap = new Adldap\Adldap($config);

$attributes = [
    'cn' => 'Doe, John',
];

$builder = $adldap->search()->newQueryBuilder();

$user = new Adldap\Models\User($attributes, $builder);

$user->save();
```

### Specific Methods available on all models

Example Query: `$model = $ad->search()->where('cn', '*')->first();`

##### Getting all raw attributes from the model:

```php
$model->getAttributes();
```

##### Getting / setting the name of the model:

```php
$model->getName();

$model->setName('New Name');
```

##### Getting / setting the model's `commonName` attribute:

```php
$model->getCommonName();

$model->setCommonName('New Common Name');
```

##### Getting the model's `sAMaccountname` attribute:

```php
$model->getAccountName();
```

##### Getting the model's `sAMaccounttype` attribute:

```php
$model->getAccountType();
```

##### Getting the model's `whencreated` attribute:

```php
$model->getCreatedAt();
```

##### Getting the model's `whenchanged` attribute:

```php
$model->getUpdatedAt();
```

##### Getting / setting the model's `distinguishedname` attribute:

```php
// Long Form

$model->getDistinguishedName();

$model->setDistinguishedName('cn=New Common Name,DC=corp,DC=acme,DC=org');

// Short Form

$model->getDn();

$model->setDn('cn=New Common Name,DC=corp,DC=acme,DC=org');
```

##### Getting the model's `objectCategory` attribute:

```php
$model->getObjectCategory();

$model->getObjectCategoryArray();

$model->getObjectCategoryDn();
```

##### Getting the model's `objectSID` attribute:

```php
$model->getObjectSID();
```

##### Getting the model's `primaryGroupId` attribute:

```php
$model->getPrimaryGroupId();
```

##### Getting the model's object class model:

```php
$objectClass = $model->getObjectClass();

$objectClass->getDn();
```

##### Getting the model's `instanceType` attribute:

```php
$model->getInstanceType();
```

##### Getting a specific raw attribute:

```php
$model->getAttribute('samAccountName');

// Or retrieving a sub-attribute if the attribute is an array

// Getting the 1st group that the model is apart of
$model->getAttribute('memberOf', 0); 
```
