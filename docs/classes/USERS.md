# Users Class

### All

To retrieve all users on AD, use the `all()` method:

    $users = $ad->users()->all();

### Search

To search for only users, call the `search()` method:

    $users = $ad->users()->search()->whereStartsWith('cn', 'John')->get();

### Find

To retrieve all information on a user, use `find()` method:

    $username = 'jdoe';
    
    $user = $ad->users()->find($username);

If you're only interested in certain LDAP fields, insert your fields in the second parameter:

    $username = 'jdoe';
    
    $select = [
        'cn',
        'memberof'
    ];
    
    $user = $ad->users()->find($username, $select);

### New Instance

To instantiate a new User, use the `newInstance()` method:

    $user = $ad->users()->newInstance();
    
    $user->setFirstName('John');
    
    $user->setLastName('Doe');
    
    if($user->save())
    {
        return 'User was successfully created.';
    }

### Create

To create a new user, use the `create()` method:

    $attributes = [
        'cn' => 'John Doe',
        'givenname' => 'John',
        'surname' => 'Doe',
    ];
    
    if($ad->users()->create($attributes))
    {
        return 'User was successfully created.';
    }

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

    $results = $ad->users()->passwordExpiry('jdoe'); // Returns array|bool
       
    $results['expires']; // Returns true / false if the users password expires
    $results['has_expired']; // Returns true / false if the users password **has** expired
    $results['expiry_timestamp']; // Returns the users password expiry date in unix time
    $results['expiry_formatted']; // Returns the users password expiry date in a formatted string ('YYYY-MM-DD HH:MM:SS')
