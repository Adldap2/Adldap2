## Computer Model

The Computer model will be returned when an LDAP entry contains an object category: `computer`.

##### Getting the computer's `operatingsystem` attribute:

    $computer->getOperatingSystem();
   
##### Getting the computer's `operatingsystemversion` attribute:

    $computer->getOperatingSystemVersion();
    
##### Getting the computer's `operatingsystemservicepack` attribute:

    $computer->getOperatingSystemServicePack();
    
##### Getting the computer's `dnshostname` attribute:

    $computer->getDnsHostName();

##### Getting the computer's `badpasswordtime` attribute:

    $computer->getBadPasswordTime();

##### Getting the computer's `accountexpires` attribute:

    $computer->getAccountExpiry();
