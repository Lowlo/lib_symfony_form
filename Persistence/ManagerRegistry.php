<?php

namespace LibSymfonyForm\Persistence;


use Doctrine\Common\Persistence\AbstractManagerRegistry;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ManagerRegistry extends AbstractManagerRegistry
{

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Fetches/creates the given services.
     *
     * A service in this context is connection or a manager instance.
     *
     * @param string $name The name of the service.
     *
     * @return object The instance of the given service.
     */
    protected function getService($name)
    {
        return $this->container->get($name);
    }

    /**
     * Resets the given services.
     *
     * A service in this context is connection or a manager instance.
     *
     * @param string $name The name of the service.
     *
     * @return void
     */
    protected function resetService($name)
    {
        $this->container->set($name, null);
    }

    /**
     * Resolves a registered namespace alias to the full namespace.
     *
     * This method looks for the alias in all registered object managers.
     *
     * @param string $alias The alias.
     *
     * @return string The full namespace.
     */
    public function getAliasNamespace($alias)
    {
        throw new \BadMethodCallException('Namespace aliases not supported.');
    }

    public function setContainer($container)
    {
        $this->container = $container;
    }
}