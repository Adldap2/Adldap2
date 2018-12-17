<?php

namespace Adldap\Models\Concerns;

use Adldap\Adldap;
use Adldap\Models\Events\Event;

trait HasEvents
{
    /**
     * Fires the specified model event.
     *
     * @param Event $event
     */
    protected function fireModelEvent(Event $event)
    {
        Adldap::getEventDispatcher()->fire($event);
    }
}
