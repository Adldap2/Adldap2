<?php

namespace Adldap\Scopes;

use Adldap\Connections\ManagerInterface;

abstract class AbstractScope
{
    /**
     * The current connection Manager instance.
     *
     * @var ManagerInterface
     */
    protected $manager;

    /**
     * Constructor.
     *
     * @param ManagerInterface $manager
     */
    public function __construct(ManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Returns the current Manager instance.
     *
     * @return ManagerInterface
     */
    public function getManager()
    {
        return $this->manager;
    }
}
