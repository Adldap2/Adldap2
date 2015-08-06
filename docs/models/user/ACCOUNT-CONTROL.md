# Setting the users User Account Control

With Adldap2, setting the users user account control is really easy and intuitive, let's get started.

First you'll need to create a new `Adldap\Objects\AccountControl` object:

    $ac = new \Adldap\Objects\AccountControl();
   
Now you can assign the account control integer by using methods.

When you're done, apply the object to the user account like so:

    $user->setUserAccountControl($ac);
    
    $user->save();

##### Run Login Script

To make the users login script run, use the method `runLoginScript()`:

    $ac->runLoginScript();
    
##### Account Is Locked

To make the users account locked, use the method `accountIsLocked()`:

    $ac->accountIsLocked();

##### Account Is Disabled

To make the users account disabled, use the method `accountIsDisabled()`:

    $ac->accountIsDisabled();
    
##### Account Is Temporary

To make the users account temporary, use the method `accountIsTemporary()`:

    $ac->accountIsTemporary();
    
##### Account Is Normal

To make the users account normal, use the method `accountIsNormal()`:

    $ac->accountIsNormal();
    
##### Account Is For Interdomain

To make the users account be trusted for a system domain that trusts other domains, use the method `accountIsForInterdomain()`:

    $ac->accountIsForInterdomain();
    
##### Account Is For Workstation

To make the users account for a workstation, use the method `accountIsForWorkstation()`:

    $ac->accountIsForWorkstation();
    
##### Account Is For Server

To make the users account for a server, use the method `accountIsForServer()`:

    $ac->accountIsForServer();
    
##### Account Is MNS

To make the users account an MNS login account, use the method `accountIsMnsLogon()`:

    $ac->accountIsMnsLogon();
    
##### Account Does Not Require Pre-Authorization

To make the user account not require pre-authorization using kerberos, use the method `accountDoesNotRequirePreAuth()`:

    $ac->accountDoesNotRequirePreAuth();
    
##### Account Requires Smart Card

To make the user account require a smart card, use the method `accountRequiresSmartCard()`:

    $ac->accountRequiresSmartCard();
    
##### Account Is Read Only

To make the user account read-only, use the method `accountIsReadOnly()`:

    $ac->accountIsReadOnly();
    
##### Home Folder Is Required

To make the users home folder required, use the method `homeFolderIsRequired()`:

    $ac->homeFolderIsRequired();
    
##### Password Is Not Required

To make the users password not required, use the method `passwordIsNotRequired()`:

    $ac->passwordIsNotRequired();
    
##### Password Cannot Be Changed

To make the users password un-changeable, use the method `passwordCannotBeChanged()`:

    $ac->passwordCannotBeChanged();
    
##### Password Does Not Expire

To make the users password have no expiration, use the method `passwordDoesNotExpire()`:

    $ac->passwordDoesNotExpire();
    
##### Allow Encrypted Password

To allow the user to send encrypted passwords, use the method `allowEncryptedTextPassword()`:

    $ac->allowEncryptedTextPassword();
    
##### Trust For Delegation

To trust the user for kerberos delegation, use the method `trustForDelegation()`:

    $ac->trustForDelegation();

##### Do Not Trust For Delegation

To **not** trust the user for kerberos delegation, use the method `doNotTrustForDelegation()`:

    $ac->doNotTrustForDelegation();

##### Trust To Auth For Delegation

This is a security-sensitive setting. Accounts that have this option enabled
should be tightly controlled. This setting lets a service that runs under the
account assume a client's identity and authenticate as that user to other remote
servers on the network.

    $ac->trustToAuthForDelegation();

##### Use DES Key Only

Restrict this principal to use only Data Encryption Standard (DES) encryption types for keys.

    $ac->useDesKeyOnly();
    
