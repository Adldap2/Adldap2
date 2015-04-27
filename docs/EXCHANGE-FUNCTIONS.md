## Exchange Functions

### All

To retrieve all exchange servers on your AD, use the `all()` method:
    
    $servers = $ad->exchange()->all();
    
You can also sort your results:

    $fields = ['cn', 'distinguishedname'];
    
    $sorted = true;
    
    $sortBy = 'cn';
    
    $sortDirection = 'desc';
    
    $servers = $ad->exchange()->all($fields, $sorted, $sortBy, $sortDirection);

### Find

To retrieve information on an exchange server, use the `find()` method:
    
    $serverName = 'EXCH-CORP';
    
    $server = $ad->exchange()->find($serverName);

### Create Mailbox

### Storage Groups

### Servers

The `servers()` method is an alias for the `all()` method, this is purely for backwards compatibility.

    $servers = $ad->exchange()->servers();

### Add Address

### Add X 400
