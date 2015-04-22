## User Functions

### All

### Find

### Info

### DN

### Create

### Modify

### Delete

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
