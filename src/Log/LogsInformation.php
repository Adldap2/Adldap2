<?php

namespace Adldap\Log;

use Psr\Log\LoggerInterface;

trait LogsInformation
{
    /**
     * The logger instance.
     *
     * @var LoggerInterface
     */
    protected static $logger;

    /**
     * Get the event dispatcher instance.
     *
     * @return LoggerInterface
     */
    public static function getLogger()
    {
        // If no logger instance has been set, well instantiate and
        // set one here. This will be our singleton instance.
        if (! isset(static::$logger)) {
            static::setLogger(new NullLogger());
        }

        return static::$logger;
    }

    /**
     * Set the logger instance.
     *
     * @param LoggerInterface $logger
     *
     * @return void
     */
    public static function setLogger(LoggerInterface $logger)
    {
        static::$logger = $logger;
    }

    /**
     * Unset the logger instance.
     *
     * @return void
     */
    public static function unsetLogger()
    {
        static::$logger = null;
    }
}
