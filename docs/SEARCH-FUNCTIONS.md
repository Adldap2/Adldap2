## Searching Functions

The new Adldap Search functionality makes it really easy to query your AD server for exactly what you want. Let's get started.

To open a new search query, call the `search()` function on your AD object like so:

    $ad = new Adldap($configuration);
    
    $ad->search();

#### All

To retrieve all entries in your AD, use the all function:

    $results = $ad->search()->all();
    
This will retrieve all entries from LDAP. However, be careful. AD has a limit of 1000 records per query (depending on your AD 
server configuration of course), if your AD has more records than this, you will only see the 1000 records AD has 
retrieved. LDAP will throw the following warning if this occurs:

    Warning: ldap_search(): Partial search results returned: Sizelimit exceeded
   
However, LDAP functions are suppressed by default, so you won't see this message. If you'd like to show errors and warnings, call the `showErrors()` method
on the AD connection before performing the search like so:

    $ad->getLdapConnection()->showErrors();
    
    $results = $ad->search()->all();

#### Where

To perform a where clause on the search object, use the `where()` function:

    $results = $ad->search()->where('cn', '=', 'John Doe')->get();
    
This query would look for an object with the common name of 'John Doe' and return the results.

We could also perform a search for all objects beginning with the common name of 'John' using the `starts_with` operator:

    $results = $ad->search()->where('cn', 'starts_with', 'John')->get();

We can also search for all objects that end with the common name of `Doe` using the `ends_with` operator:

    $results = $ad->search()->where('cn', 'ends_with', 'Doe')->get();

We can also search for all objects with a common name that contains `John Doe` using the `contains` operator:

    $results = $ad->search()->where('cn', 'contains', 'John Doe')->get();

Or we can retrieve all objects that contain a common name attribute using the wildcard operator (`*`):

    $results = $ad->search()->where('cn', '*')->get();

This type of filter syntax allows you to clearly see what your searching for.

Remember, fields are case insensitive, so it doesn't matter if you use `->where('CN', '*')` or `->where('cn', '*')`,
they would return the same result.
   
It's also good to know that all values inserted into a where, or an orWhere method,
<b>are escaped</b> by default into a hex string, so you don't need to worry about escaping them. For example:

    $query = $ad->search()->where('cn', '=', 'test//un-escaping//')->getQuery();
    
    // Returns '(cn=\74\65\73\74\2f\2f\75\6e\2d\65\73\63\61\70\69\6e\67\2f\2f)'

#### Or Where

To perform an 'or where' clause on the search object, use the `orWhere()` function. However, please be aware this
function performs differently than it would on a database. For example:

    $results = $ad->search()
            ->where('cn', '=', 'John Doe')
            ->orWhere('cn' '=', 'Suzy Doe')
            ->get();
    
This query would return no results, because we're already defining that the common name (`cn`) must equal `John Doe`. Applying
the `orWhere()` does not amount to 'Look for an object with the common name as "John Doe" OR "Suzy Doe"'. This query would
actually amount to 'Look for an object with the common name that <b>equals</b> "John Doe" OR "Suzy Doe"

To solve the above problem, we would use `orWhere()` for both fields. For example:

    $results = $ad->search()
            ->orWhere('cn', '=', 'John Doe')
            ->orWhere('cn' '=', 'Suzy Doe')
            ->get();
    
Now, we'll retrieve both John and Suzy's AD records, because the common name can equal either.

For another example, what if we wanted to retrieve 

#### Select

If you'd like to include only certain fields in your search results, supply a string or an array to the `select()` method
like so:

    // Selecting one field
    $results = $ad->search()->select('cn')->all();
    
    // Selecting multiple fields
    $results = $ad->search()->select(array('cn', 'displayname'))->all();

All searches will return *all* information for each entry. Be sure to use `select($fields = array())` when you only
need a small amount of information.

#### Sort By

If you'd like to sort your returned results, call the `sortBy()` method like so:
    
    // Returned results will be sorted by the common name in a descending order
    $results = $ad->search()->where('cn', '=', 'John*')->sortBy('cn', 'desc')->get();

The function is case insensitive with directions, so don't worry if you use `DESC` or `desc`.

#### Query

To perform a raw LDAP query yourself, use the `query()` method:

    $results = $ad->search()->query('(cn=John Doe)');
    
However, keep in mind the inserted query is not escaped. If you need to escape your values before the query, be sure
to do so using:

    $escapedValue = $ad->getLdapConnection()->escape('John Doe');
    
Then you can perform the above query like so:

    $results = $ad->search()->query("(cn=$escapedValue)");

#### Paginate

Pagination is useful when you have a limit on the returned results from LDAP. Using pagination, you will successfully be able
to view all LDAP results. To paginate your results, call the `paginate()` method:

    $perPage = 25;
    
    $currentPage = $_GET['page'];
    
    $results = $ad->search()->where('objectClass', '=', 'person')->paginate($perPage, $currentPage);
    
<b>It's also good to know, that the current page starts at zero (zero being the first page).</b> If you'd like to present pages
differently, feel free to do so.

Paginating a search result will return a `Adldap\Objects\Paginator` instance. This object provides some handy functions:

    $results = $ad->search()->where('objectClass', '=', 'person')->paginate($perPage, $currentPage);
    
    $results->getPages(); // Returns total number of pages, int
    
    $results->getCurrentPage(); // Returns current page number, int
    
    $results->getPerPage(); // Returns the amount of entries allowed per page, int
    
    $results->count(); // Returns the total amount of retrieved entries, int
    
    // Iterate over the results like normal
    
    foreach($results as $result)
    {
        echo $result['cn'];
    }

#### Recursive

By default, all searches performed are recursive. If you'd like to disable recursive search, use the `recursive()` method:

    $result = $ad->search()->recursive(false)->all();
    
This would perform an `ldap_listing()` instead of an `ldap_search()`.

#### Read

If you'd like to perform a read instead of a listing or a recursive search, use the `read()` method:

    $result = $ad->search()->read(true)->where('objectClass', '*')->get();
    
This would perform an `ldap_read()` instead of an `ldap_listing()` or an `ldap_search()`.

#### Get Query

If you'd like to retrieve the current query to save or run it at another time, use the `getQuery()` method:

    $query = $ad->search()->where('cn', '=', 'John Doe')->getQuery();
    
    echo $query; // Returns '(cn=\4a\6f\68\6e\20\44\6f\65)'
    
#### Get Wheres

If you'd like to retrieve the current wheres on the search object, call the `getWheres()` method:

    $wheres = $ad->search()->where('cn', '=', 'John')->getWheres();
    
    var_dump($wheres);
    
#### Get Or Wheres

If you'd like to retrieve the current or wheres on the search object, call the `getOrWheres()` method:

    $orWheres = $ad->search()->orWhere('cn', '=', 'John')->getOrWheres();
    
    var_dump($orWheres);

#### Get Selects

To retrieve the current selected fields in on the search object, use the `getSelects()` method:

    $selects = $ad->search()->select(array('cn', 'dn'))->getSelects();
    
    var_dump($selects);
    
#### Has Selects

If you need to know if the search object currently contains selected fields, use the `hasSelects()` function:

    echo $ad->search()->select('cn')->hasSelects(); // Returns true
    
### Examples

#### User Examples

Retrieving all users who <b>do not</b> have the common name of 'John':

    $results = $ad->search()
            ->where('objectClass', '=', $ad->getUserIdKey())
            ->where('cn', '!', 'John')
            ->get();
    
Retrieving all users who do not have the common name of 'John' or 'Suzy':

    $results = $ad->search()
                ->where('objectClass', '=', $ad->getUserIdKey())
                ->orWhere('cn', '!', 'John')
                ->orWhere('cn', '!', 'Suzy')
                ->get();
           
Retrieving all users who have a mail account:

    $results = $ad->search()
                    ->where('objectClass', '=', $ad->getUserIdKey())
                    ->where('mail', '*')
                    ->get();
                    
#### Computer Examples

Retrieving a all computers:

    $results = $ad->search()
            ->where('objectClass', '=', 'computer')
            ->get();
            
Retrieving all computers that run Windows 7:

    $results = $ad->search()
            ->where('objectClass', '=', 'computer')
            ->where('operatingSystem', 'starts_with', 'Windows 7')
            ->get();

#### Folder (OU) examples

Retrieving a folder:

    $folderName = 'Accounting';
    
    $results = $this->adldap->search()
                ->where('OU', '=', $folderName)
                ->first();
