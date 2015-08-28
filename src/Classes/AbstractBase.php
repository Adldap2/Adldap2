<?php

namespace Adldap\Classes;

use Adldap\Query\Grammar;
use Adldap\Query\Builder;
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
     */
    public function boot()
    {
        //
    }

    /**
     * Returns a new query builder instance.
     *
     * @return Builder
     */
    public function newBuilder()
    {
        return new Builder($this->newGrammar());
    }

    /**
     * Returns a new query grammar instance.
     *
     * @return Grammar
     */
    public function newGrammar()
    {
        return new Grammar();
    }
}
