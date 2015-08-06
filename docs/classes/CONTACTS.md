## Contacts Class

### All

To retrieve all contacts from AD, use the `all()` method:

    $contacts = $ad->contacts()->all();

### Find

To retrieve information on a specific contact, use the `find()` method:

    $contact = $ad->contacts()->find('John Doe');

If you're only interested in certain LDAP fields, insert your fields in the second parameter:

    $fields = [
        'cn'
    ];

    $contact = $ad->contacts()->find('John Doe', $fields);

### Create

### Modify

### Groups

### In Group
