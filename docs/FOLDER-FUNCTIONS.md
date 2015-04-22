## Folder Functions

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

