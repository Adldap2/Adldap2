## Computer Functions

### All

To retrieve all computers on your AD server, use the `all()` method:

    $results = $ad->computer()->all();

You can also customize your results by providing some paramters inside the function like so:

    $fields = array('operatingsystem');
    
    $results = $ad->computer()->all($fields, $sorted = true, $sortBy = 'cn', $sortByDirection = 'asc');

### Find

To retrieve information on a specific computer, use the `find()` method:

    $computer = $ad->computer->find('WIN-PC');
    
You can also customize the fields that are returned by passing in field array in the second parameter:

    $fields = array('operatingAystem', 'operatingsystemversion');
    
    $computer = $ad->computer()->find('WIN-PC', $fields);
    
### Info

To preserve backwards compatibility, the `info()` function is an alias for the `find()` method:

    $computer = $ad->computer()->info('WIN-PC', $fields);

### DN

To retrieve a computers distinguished name, use the `dn()` method:

    $computerDn = $ad->computer()->dn('WIN-PC');
    
    echo $computerDn;

### Groups

To retrieve all the groups that a computer is in, use the `groups()` method:

    $groups = $ad->computer()->groups('WIN-PC');
    
### In Group

To determine if a computer is in a group, use the `inGroup()` method:

    $inGroup = $ad->computer()->inGroup('WIN-PC', 'Accounting'); // Returns true / false
