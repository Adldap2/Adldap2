## Computers Class

### All

To retrieve all computers on your AD server, use the `all()` method:

    $results = $ad->computer()->all();

You can also customize your results by providing some paramters inside the function like so:

    $fields = array('operatingsystem');
    
    $results = $ad->computer()->all($fields, $sorted = true, $sortBy = 'cn');

### Find

To retrieve information on a specific computer, use the `find()` method:

    $computer = $ad->computer->find('WIN-PC');
    
You can also customize the fields that are returned by passing in field array in the second parameter:

    $fields = array('operatingsystem', 'operatingsystemversion');
    
    $computer = $ad->computer()->find('WIN-PC', $fields);

### Search

### New Instance

### Create
