## User Functions

### All

To retrieve all users on AD, use the `all()` method:

    $users = $ad->user()->all();

### Find

To retrieve all information on a user, use `find()` method:

    $username = 'jdoe';
    
    $user = $ad->user()->find($username);

If you're only interested in certain LDAP fields, insert your fields in the second parameter:

    $username = 'jdoe';
    
    $fields = [
        'cn',
        'memberof'
    ];
    
    $user = $ad->user()->find($username, $fields);
    
    echo $user['cn'];
    echo $user['memberof'];
   
### Info

The `info()` method is an alias for the `find()` method, this exists for backwards compatibility.

    $username = 'jdoe';
    
    $user = $ad->user()->info($username);

### DN

To retrieve a users full distinguished name, use the `dn()` method:

    $username = 'jdoe';

    $dn = $ad->user()->dn($username);

### Create

### Modify

### Delete

To delete a user, use the `delete()` method:
    
    $username = 'jdoe';
    
    $deleted = $ad->user()->delete($username);

### Change Password

To change a users password, use the `changePassword()` method:

    try
    {
        $newPassword = 'newpassword123';
        
        $oldPassword = 'oldpassword123';
    
        $changed = $ad->user()->changePassword('jdoe', $newPassword, $oldPassword);
        
    } catch(Adldap\Exceptions\WrongPasswordException $e)
    {
        return "Uh oh, you've entered the wrong old password!";
    } catch(Adldap\Exceptions\PasswordPolicyException $e)
    {
        return "Looks like your new password doesn't meet our requirements. Try again."
    }

### Password Expiry

To retrieve a users password expiry date, use the `passwordExpiry()` method:

    $results = $ad->user()->passwordExpiry('jdoe'); // Returns array|bool
       
    $results['expires']; // Returns true / false if the users password expires
    $results['has_expired']; // Returns true / false if the users password **has** expired
    $results['expiry_timestamp']; // Returns the users password expiry date in unix time
    $results['expiry_formatted']; // Returns the users password expiry date in a formatted string ('YYYY-MM-DD HH:MM:SS')
    
### Get Last Logon

To retrieve a users last login time, use the `getLastLogon()` method:

    $time = $ad->user()->getLastLogon('jdoe'); // Returns in Unix time
    
    $date = date('Y-m-d h:i:s', $time);
