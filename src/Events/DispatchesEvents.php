<?php

namespace Adldap\Events;

trait DispatchesEvents
{
    /**
     * @var DispatcherInterface
     */
    protected static $dispatcher;

    /**
     * Get the event dispatcher instance.
     *
     * @return DispatcherInterface
     */
    public static function getEventDispatcher()
    {
        return static::$dispatcher;
    }

    /**
     * Set the event dispatcher instance.
     *
     * @param DispatcherInterface $dispatcher
     *
     * @return void
     */
    public static function setEventDispatcher(DispatcherInterface $dispatcher)
    {
        static::$dispatcher = $dispatcher;
    }

    /**
     * Unset the event dispatcher for models.
     *
     * @return void
     */
    public static function unsetEventDispatcher()
    {
        static::$dispatcher = null;
    }
}
