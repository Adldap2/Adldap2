<?php

namespace Adldap\Log;

use Psr\Log\AbstractLogger;

class NullLogger extends AbstractLogger
{
    /**
     * {@inheritdoc}
     */
    public function log($level, $message, array $context = array())
    {
        // Do nothing.
    }
}
