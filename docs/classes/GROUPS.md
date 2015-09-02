## Groups class

### All

To retrieve all groups, use the `all()` method:

    $groups = $ad->groups()->all();

### Search

To search for groups, use the `search()` method:

    $groups = $ad->groups()->search()->whereContains('cn', 'Managers')->get();

### Find

To find a group, use the `find()` method:

    $group = $ad->groups()->find('Accounting');

### New Instance

To create a new Group instance, use the `newInstance()` method:

    $attributes = [
        'cn' => 'Accounting Department',
    ];

    $group = $ad->groups()->newInstance($attributes);
    
    if($group->save())
    {
        // Group has been created!
    } else
    {
        // Looks like there was an issue saving this group
    }

### Create

To create a group, use the `create()` method:

    $attributes = [
        'cn' => 'Accounting Department',
        'dn' => 'cn=Accounting Department,dc=corp,dc=Acme,dc=org',
    ];
    
    if($ad->groups()->create($attributes))
    {
        // Group has been created!
    }
