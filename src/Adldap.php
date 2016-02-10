<?php

namespace Adldap;

use Adldap\Connections\Manager;
use Adldap\Contracts\AdldapInterface;
use Adldap\Contracts\Connections\ManagerInterface;

class Adldap implements AdldapInterface
{
    /**
     * Stores the current Manager instance.
     *
     * @var ManagerInterface
     */
    protected $manager;

    /**
     * {@inheritdoc}
     */
    public function __construct(ManagerInterface $manager = null)
    {
        if (is_null($manager)) {
            $this->setManager(new Manager());
        } else {
            $this->setManager($manager);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * {@inheritdoc}
     */
    public function setManager(ManagerInterface $manager)
    {
        $this->manager = $manager;

        return $this;
    }

    /**
     * Retrieves a provider from the connection Manager.
     *
     * @param string $name
     *
     * @return Contracts\Connections\ProviderInterface
     */
    public function getProvider($name)
    {
        return $this->getManager()->get($name);
    }

    /**
     * {@inheritdoc}
     */
    public function connect($connection, $username = null, $password = null)
    {
        $manager = $this->getManager();

        $provider = $manager->get($connection);

        $provider->connect($username, $password);

        return $provider;
    }
}
