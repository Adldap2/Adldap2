## Group Functions

### Get Primary Group

To retrieve a users primary group (due to LDAP not returning the distinguished name of their primary group), use the
`getPrimaryGroup()` method:

    $user = $ad->user()->find('John Doe');
    
    $primaryGroupId = $user['primarygroupid'];
    
    $objectSid = $user['objectsid'];
    
    $groupDn = $ad->group()->getPrimaryGroup($primaryGroupId, $objectSid);
    
    echo $groupDn; // Returns 'CN=Domain Users,CN=Users,DC=corp,DC=acme,DC=org'
