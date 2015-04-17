## Searching Functions

The new Adldap Search functionality makes it really easy to query your AD server for exactly what you want. Let's get started.

To open a new search query, call the `search()` function on your AD object like so:

    $ad = new Adldap($configuration);
    
    $ad->search();
    
#### All

To retrieve all entries in your AD, use the all function:

    $results = $ad->search()->all();
    
This will retrieve all entries from LDAP. However, be careful. AD has a limit of 1000 records per query, if your AD has
more records than this, you will only see the 1000 records AD has retrieved. LDAP will throw the following warning if
this occurs:

    Warning: ldap_search(): Partial search results returned: Sizelimit exceeded
   
However, LDAP functions are suppressed by default, so you won't see this message. If you'd like to show errors and warnings, call the `showErrors()` method
on the AD connection before performing the search like so:

    $ad->getLdapConnection()->showErrors();
    
    $results = $ad->search()->all();

#### Where

To perform a where clause on the search object, use the `where()` function:

    $results = $ad->search()->where('cn', '=', 'John Doe')->get();
    
This query would look for an object with the common name of 'John Doe' and return the results.

We could also perform a search for all objects beginning with the common name of 'John' using the asterisk (*) wildcard:

    $results = $ad->search()->where('cn', '=', 'John*')->get();
    
Or we can retrieve all objects that contain a common name:

    $results = $ad->search()->where('cn', '*')->get();

#### Or Where

To perform an 'or where' clause on the search object, use the `orWhere()` function. However, please be aware this
function performs differently than it would on a database. For example:

    $results = $ad->search()->where('cn', '=', 'John Doe')->orWhere('cn' '=', 'Suzy Doe')->get();
    
This query would return no results, because we're already defining that the common name (`cn`) must equal `John Doe`. Applying
the `orWhere()` does not amount to 'Look for an object with the common name as "John Doe" OR "Suzy Doe"'.

To solve the above problem, we would use `orWhere()` for both fields. For example:

    $results = $ad->search()->orWhere('cn', '=', 'John Doe')->orWhere('cn' '=', 'Suzy Doe')->get();
    
Now, we'll retrieve both John and Suzy's AD records, because the common name can equal either.

#### Select

#### Get Query

#### Get Wheres

#### Get Selects