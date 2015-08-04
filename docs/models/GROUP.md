## Group Functions

### Get Primary Group

To retrieve a users primary group (due to LDAP not returning the distinguished name of their primary group), use the
`getPrimaryGroup()` method:

    // Get the users information
    $user = $ad->user()->find('John Doe');
    
    // Get their primary group ID
    $primaryGroupId = $user['primarygroupid'];
    
    // Get their object SID
    $objectSid = $user['objectsid'];
    
    /*
     * Get the primary groups DN by 
     * passing in the users primary group ID
     * and SID
     */
    $groupDn = $ad->group()->getPrimaryGroup($primaryGroupId, $objectSid);
    
    echo $groupDn; // Returns 'CN=Domain Users,CN=Users,DC=corp,DC=acme,DC=org'
