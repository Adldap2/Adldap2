## User Model

The user model will be returned when an AD contains the object category of `person`.

### Methods and Attribute Setters / Getters

##### Getting / setting the users' `title` attribute:

    $user->getTitle();
    
    $user->setTitle('Manager');

##### Getting / setting the users' `department` attribute:

    $user->getDepartment();
    
    $user->setDepartment('Accounting');

##### Getting / setting the users' `givenName` attribute:

    $user->getFirstName();
    
    $user->setFirstName('John');

##### Getting / setting the users' `surname` attribute:

    $user->getLastName();
    
    $user->setLastName('Doe');

##### Getting / setting the users' `telephone` attribute:

    $user->getTelephoneNumber();
    
    $user->setTelephoneNumber('555 555-5555');

##### Getting / setting the users' `company` attribute:

    $user->getCompany();
    
    $user->setCompany('Acme');

##### Getting / setting the users' `mail` attribute:

    $user->getEmail();
    
    $user->setEmail('jdoe@acme.org');

##### Getting / setting the users' `mail` attribute (multiple):

    $user->getEmails();
    
    $user->setEmails(['jdoe@acme.org', 'johndoe@otheremail.com']);
    
##### Getting / setting the users' `useraccountcontrol` attribute:

    $user->getUserAccountControl();
    
    $user->setUserAccountControl(new AccountControl());
        
[More about this here](https://github.com/Adldap2/Adldap2/blob/master/docs/models/user/ACCOUNT-CONTROL.md)

##### Getting the users' `homeMdb` attribute:

    $user->getHomeMb();
    
##### Getting the users' `mailnickname` attribute:

    $user->getMailNickname();
    
##### Getting the users' `userprincipalname` attribute:

    $user->getUserPrincipalName();
    
##### Getting the users' `proxyaddresses` attribute:

    $user->getProxyAddresses();
    
##### Getting the users' `scriptpath` attribute:

    $user->getScriptPath();
    
##### Getting the users' `badpwdcount` attribute:

    $user->getBadPasswordCount();
    
##### Getting the users' `badpasswordtime` attribute:

    $user->getBadPasswordTime();
    
##### Getting the users' `pwdlastset` attribute:

    $user->getPasswordLastSet();
    
##### Getting the users' `lockouttime` attribute:

    $user->getLockoutTime();
    
##### Getting the users' `profilepath` attribute:

    $user->getProfilePath();
    
##### Getting the users' `legacyexchangedn` attribute:

    $user->getLegacyExchangeDn();

##### Getting the users' `accountexpires` attribute:

    $user->getAccountExpiry();
    
##### Getting the users' `showinaddressbook` attribute:

    $user->getShowInAddressBook();
