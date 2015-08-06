## Working With Distinguished Names

Working with DN strings are a pain, but they're about to get easier. Adldap includes a DN builder for easily modifying and
creating DN strings.

#### Creating a DN

When you're creating a new AD record, you'll need to create a distinguished name as well. Let's go through an example of
creating a new user.

    $user = $ad->users()->newInstance();
    
    $user->setCommonName('John Doe');
    $user->setFirstName('John');
    $user->setLastName('Doe');
   
So we've set the basic information on the user, but we run into trouble when we want to put the user into a certain container
(such as 'Accounting') which is done through the DN. Let's go through this example:

    $dn = $user->getDnBuilder();
    
    $dn->addCn($user->getCommonName());
    $dn->addOu('Accounting');
    $dn->addDc('corp');
    $dn->addDc('acme');
    $dn->addDc('org');
    
    echo $dn->get(); // Returns 'cn=John Doe,ou=Accounting,dc=corp,dc=acme,dc=org'
    
Now we've built a DN, and all we have to do is set it on the new user:    
    
    $user->setDn($dn);
    
    $user->save();

#### Modifying a DN
