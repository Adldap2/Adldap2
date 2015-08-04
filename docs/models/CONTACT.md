## Contact Functions

### All

To retrieve all contacts from AD, use the `all()` method:

    $contacts = $ad->contact()->all();

### Find

To retrieve information on a specific contact, use the `find()` method:

    $contact = $ad->contact()->find('John Doe');

If you're only interested in certain LDAP fields, insert your fields in the second parameter:

    $fields = [
        'cn'
    ];

    $contact = $ad->contact()->find('John Doe', $fields);

### Info

The `info()` method is an alias for the `find()` method:

    $contact = $ad->contact()->info('John Doe', $fields);

### DN

To retrieve a contacts DN, use the `dn()` method:

    $contactDn = $ad->contact()->dn('John Doe');
    
    echo $contactDn;

### Create

### Modify

### Delete

To delete a contact, use the `delete()` method:

    $contactDn = $ad->contact()->dn('John Doe');
    
    if($contactDn) {
        $deleted = $ad->contact()->delete($contactDn);
    }

### Groups

### In Group

### Contact Mail Enable
