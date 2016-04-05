<?php

namespace Adldap;

use Adldap\Connections\Manager;
use Adldap\Contracts\AdldapInterface;
use Adldap\Contracts\Connections\ManagerInterface;
use Adldap\Contracts\Connections\ProviderInterface;

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
     * {@inheritdoc}
     */
    public function addProvider($name, ProviderInterface $provider)
    {
        return $this->manager->add($name, $provider);
    }

    /**
     * {@inheritdoc}
     */
    public function getProvider($name)
    {
        return $this->manager->get($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultProvider()
    {
        return $this->manager->getDefault();
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
