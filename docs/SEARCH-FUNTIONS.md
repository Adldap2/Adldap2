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
   
However, ldap functions are suppressed by default. If you'd like to show errors and warnings, call the `showErrors()` method
on the AD connection like so:

    $ad->getLdapConnection()->showErrors();
    
    $results = $ad->search()->all();
    
#### Where

To perform a where clause on the search object, use the `where()` function:

    $results = $ad->search()->where('cn', '=', 'John Doe')->get();
    
This query would look for an object with the common name of 'John Doe' and return the results.

#### Or Where

#### Select

#### Get Query

#### Get Wheres

#### Get Selects