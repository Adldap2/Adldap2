## Group Model

The Computer model will be returned when an LDAP entry contains an object category: `group`.

##### Getting / setting the groups `member` attribute:

    $group->getMembers();
    
    $members = [
        $user1->getDn(),
        $user2->getDn(),
    ];
    
    $group->setMembers($members);
    
##### Adding / Removing a single member:

    $group->addMember($user);
    
    $group->removeMember($user);
    
##### Removing all members:

    $group->removeMembers();
    
##### Getting the groups `grouptype` attribute:

    $group->getGroupType();

