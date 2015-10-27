<?php

namespace Adldap\Classes;

use Adldap\Connections\ManagerInterface;

abstract class AbstractBase
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

        $this->boot();
    }

    /**
     * Returns the current Adldap instance.
     *
     * @return ManagerInterface
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * Overridable method that is called upon construct.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
