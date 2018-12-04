<?php

namespace Adldap\Models\Concerns;

use Adldap\Models\Events\Event;
use Adldap\Events\DispatchesEvents;

trait HasEvents
{
    use DispatchesEvents;

    /**
     * Fires the specified model event.
     *
     * @param Event $event
     */
    protected function fireModelEvent(Event $event)
    {
        static::getEventDispatcher()->fire($event);
    }
}
