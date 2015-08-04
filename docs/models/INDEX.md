# Models

When receiving search results from Adldap, you will always have an array of models depending on the type of AD entry.

## Types of Models

> Note: All models are located in the namespace `Adldap\Models\`.

- [User](https://github.com/Adldap2/Adldap2/blob/master/docs/models/USER.md)
- [Contact](https://github.com/Adldap2/Adldap2/blob/master/docs/models/CONTACT.md)
- [Group](https://github.com/Adldap2/Adldap2/blob/master/docs/models/GROUP.md)
- [Container](https://github.com/Adldap2/Adldap2/blob/master/docs/models/CONTAINER.md)
- [Computer](https://github.com/Adldap2/Adldap2/blob/master/docs/models/COMPUTER.md)
- [Exchange Server](https://github.com/Adldap2/Adldap2/blob/master/docs/models/EXHANGE-SERVER.md)

## Modifying Models

To modify any attributes on the model, you can just set the attributes manually, or use their [setters](#specific-methods-available-on-all-models):

    // Using a setter
    $user->setCommonName('Doe, John');
    $user->setFirstName('John');
    $user->setLastName('Doe');
    
    // Manually modifying, using their AD attributes
    $user->cn = 'Doe, John';
    $user->givenName = 'John';
    $user->surname = 'Doe';

Adldap is smart enough to know that if an attribute doesn't exist, then it will add it to the entry.

## Saving / Updating

When you've made your modifications, just call the `save()` method. It will return true or false if the record has been
saved or not:

    $user->setFirstName('First Name');
    
    if($user->save())
    {
        // Successfully updated record
    } else
    {
        // There was an issue updating this record
    }

## Manually Creating a new Model Instance

To manually create a new model instance, you need to inject the current Adldap instance into the Models second parameter:

    $adldap = new Adldap($config);
    
    $attributes = [
        'cn' => 'Doe, John',
    ];
    
    $user = new Adldap\Models\User($attributes, $adldap);
    
    $user->save();

## Specific Methods available on all models

Example Query: `$model = $ad->search()->where('cn', '*')->first();`

Getting all raw attributes from the model:

    $model->getAttributes();

Getting / setting the name of the model:

    $model->getName();
    
    $model->setName('New Name');

Getting / setting the model's `commonName` attribute:

    $model->getCommonName();
    
    $model->setCommonName('New Common Name');

Getting the model's `sAMaccountname` attribute:

    $model->getAccountName();

Getting the model's `sAMaccounttype` attribute:

    $model->getAccountType();

Getting the model's `whencreated` attribute:

    $model->getCreatedAt();

Getting the model's `whenchanged` attribute:

    $model->getUpdatedAt();

Getting / setting the model's `distinguishedname` attribute:

    // Long Form
    $model->getDistinguishedName();
    
    $model->setDistinguishedName(cn=New Common Name,DC=corp,DC=acme,DC=org');
       
    // Short Form
   
    $model->getDn();
    
    $model->setDn('cn=New Common Name,DC=corp,DC=acme,DC=org');

Getting the model's `objectCategory` attribute:

    $model->getObjectCategory();
    
    $model->getObjectCategoryArray();
    
    $model->getObjectCategoryDn();

Getting the model's `objectSID` attribute:

    $model->getObjectSID();

Getting the model's `primaryGroupId` attribute:

    $model->getPrimaryGroupId();

Getting the model's object class model:

    $objectClass = $model->getObjectClass();
    
    $objectClass->getDn();
    
Getting the model's `instanceType` attribute:

    $model->getInstanceType();

Getting a specific raw attribute:

    $model->getAttribute('samAccountName');
    
    // Or retrieving a sub-attribute if the attribute is an array
    
    // Getting the 5th group that the model is apart of
    $model->getAttribute('memberOf', 5); 

