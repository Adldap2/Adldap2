# Events

Adldap2 events provide a method of listening for certain LDAP actions
that are called and execute tasks for that specific event.

> **Note**: The Adldap2 event dispatcher was actually taken from the
> [Laravel Framework](https://github.com/laravel/framework) with
> Broadcasting & Queuing omitted to remove extra dependencies
> that would be required with implementing those features.
>
> If you've utilized Laravel's event system before, this will feel very familiar.

## Registering Listeners

To register a listener on an event, first you will need to retrieve the event dispatcher from Adldap2:

```php
use Adldap\Adldap;

$dispatcher = Adldap::getEventDispatcher();
```

Once you retrieve the listener, you can listen to specific events via the `listen()` method.

The first argument is the event name you would like to listen for, and the second is
either a closure or class name that should handle the event:

```php
use Adldap\Adldap;
use Adldap\Auth\Events\Binding;

$dispatcher = Adldap::getEventDispatcher();

$dispatcher->listen(Binding::class, function (Binding $event) {
    // Do something with the Binding event information:
    
    $event->connection; // Adldap\Connections\Ldap instance
    $event->username; // 'jdoe@acme.org'
    $event->password; // 'super-secret'
});
```

Using a class:

> **Note**: When using just a class name, the class must contain a public `handle()` method that will handle the event.

```php
use Adldap\Adldap;
use Adldap\Auth\Events\Binding;

$dispatcher = Adldap::getEventDispatcher();

$dispatcher->listen(Binding::class, MyApp\BindingEventHandler::class);
```

```php
namespace MyApp;

use Adldap\Auth\Events\Binding;

class BindingEventHandler
{
    public function handle(Binding $event)
    {
        // Handle the event...
    }
}
```

## List of Events

