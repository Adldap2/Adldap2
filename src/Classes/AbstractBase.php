<?php

namespace Adldap\Classes;

use Adldap\Adldap;

abstract class AbstractBase
{
    /**
     * The current Adldap connection via dependency injection.
     *
     * @var Adldap
     */
    protected $adldap;

    /**
     * Constructor.
     *
     * @param Adldap $adldap
     */
    public function __construct(Adldap $adldap)
    {
        $this->adldap = $adldap;

        $this->boot();
    }

    /**
     * Returns the current Adldap instance.
     *
     * @return Adldap
     */
    public function getAdldap()
    {
        return $this->adldap;
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
