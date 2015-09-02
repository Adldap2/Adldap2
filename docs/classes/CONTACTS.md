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

### New Instance

To create a new Contact instance, use the `newInstance()` method:

    $attributes = [
        'cn' => 'John Doe',
        'dn' => 'cn=Contact,dc=corp,dc=acme,dc=org',
    ];

    $contact = $ad->contacts()->newInstance($attributes);
    
    if ($contact->save())
    {
        // Contact was saved!
    }

### Create

To create a contact, use the `create()` method:

    $attributes = [
        'cn' => 'John Doe',
        'dn' => 'cn=Contact,dc=corp,dc=acme,dc=org',
    ];
    
   if ($ad->contacts()->create($attributes))
   {
        // Contact was saved!
   }
