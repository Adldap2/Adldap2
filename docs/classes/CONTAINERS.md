## Container Class

Container functions are used for managing active directory OU's (organizational units).

### All

To retrieve all OU's from AD, use the `all()` method:

    $folders = $ad->containers()->all();
    
Keep in mind that this query is by default recursive, and your results may be limited depending on your server.

### Find

To find a folder, use the `find()` method:

    $folder = $ad->containers()->find('Accounting');
