## Container Class

Container functions are used for managing active directory OU's (organizational units).

### All

To retrieve all OU's from AD, use the `all()` method:

    $containers = $ad->containers()->all();
    
Keep in mind that this query is by default recursive, and your results may be limited depending on your server.

### Search

To search for only containers, use the `search()` method:

    $containers = $ad->containers()->search()->whereEquals('cn', 'Accounting')->get();

### Find

To find a folder, use the `find()` method:

    $containers = $ad->containers()->find('Accounting');

### New Instance

### Create
