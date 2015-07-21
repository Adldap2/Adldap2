## Configuration

Configuring Adldap is easy. First create a Configuration object:

    $config = new \Adldap\Connections\Configuration();
    
Once you've created one, you can apply options to it through its methods.

### Admin Username

    $config->setAdminUsername('admin');
    
### Admin Password

    $config->setAdminPassword('correcthorsebatterystaple');

### Domain Controllers
    
    $controllers = ['dc01.corp.company.org', 'dc02.corp.company.org'];
    
    $config->setDomainControllers($controllers);

### Base Distinguished Name

    $config->setBaseDn('DC=corp,DC=company,DC=org');

### Port

> Note, the port is set automatically depending on the
> configured protocol, you, only change it if your AD server has a unique port.

    $config->setPort(389);

### Use SSL

    $config->setUseSSL(true);

### Use TLS

    $config->setUseTLS(true);

### Use SSO

    $config->setUseSSO(true);



