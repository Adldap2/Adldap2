## Contacts Class

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

### Create

### Modify

### Groups

### In Group
