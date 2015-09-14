## Exchange Class

### All

To retrieve all exchange servers on your AD, use the `all()` method:
    
    $servers = $ad->exchange()->all();
    
You can also sort your results:

    $fields = ['cn', 'distinguishedname'];
    
    $sorted = true;
    
    $sortBy = 'cn';
    
    $sortDirection = 'desc';
    
    $servers = $ad->exchange()->all($fields, $sorted, $sortBy, $sortDirection);

### Search

To search for exchange servers, use the `search()` method:

    $servers = $ad->exchange()->search()->whereStartsWith('cn', 'MAIL01')->get();

### Find

To retrieve information on an exchange server, use the `find()` method:
    
    $serverName = 'EXCH-CORP';
    
    $server = $ad->exchange()->find($serverName);

### Storage Groups

### Storage Databases
