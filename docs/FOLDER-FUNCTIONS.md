## Folder Functions

Folder functions are used for managing OU's (organizational units).

### All

To retrieve all OU's from AD, use the `all()` method:

    $folders = $ad->folder()->all();
    
Keep in mind that this query is by default recursive, and your results may be limited depending on your server.

### Listing

    listing($folders = array(), $dnType = Adldap::ADLDAP_FOLDER, $recursive = NULL, $type = NULL)

To retrieve an OU's (organizational unit(s)) listing, use the `listing()` function. However keep in mind that this query is by default recursive!
Calling this function without listing an OU will retrieve all entries recursively.

Retrieving all data from your base DN (**not recommended if you have a large AD**):

    $results = $ad->folder()->listing();

Retrieving all data from the OU `Acme`:

    $results = $ad->folder()->listing(array('Acme'));

To navigate an OU, pass in deeper OUs into the array. For example:

    $results = $ad->folder()->listing(array('Acme', 'Users', 'Accounting'));
    
    // The searched DN would be: 'OU=Accounting,OU=Users,OU=Acme,DC=corp,DC=acme,DC=com'

It's also good to note, that **OUs must be spelt exact**. If an OU is not spelled correctly, you will receive zero results.

### Find

To find a folder, use the `find()` method:

    $folder = $ad->folder()->find('Accounting');

### Create

To create a folder, use the `create()` method:

    $attributes = [
        'ou_name' => 'Employees',
        'container' => [
            'Users'
        ]
    ];

### Delete

To delete a distinguished name, use the `delete()` method:

    $distinguishedName = 'OU=Accounting,OU=User Accounts,DC=corp,DC=acme,DC=com';
    
    $deleted = $ad->folder()->delete($distinguishedName);

You can easily delete a found folder like so:

    $folder = $ad->folder()->find('Accounting');
    
    if(is_array($folder) && array_key_exists('dn', $folder)) {
        
        $ad->folder()->delete($folder['dn']);
        
    }
