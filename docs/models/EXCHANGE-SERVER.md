## Exchange Server Model

The Computer model will be returned when an LDAP entry contains an object category: `ms-exch-exchange-server`.

##### Getting the exchange servers `serialnumber` attribute:

    $es->getSerialNumber();
    
##### Getting the exchange servers `versionnumber` attribute:

    $es->getVersionNumber();

##### Getting the exchange servers `admindisplayname` attribute:

    $es->getAdminDisplayName();
    
##### Getting the exchange servers `messagetrackingenabled` attribute: 
    
    $es->getMessageTrackingEnabled();
