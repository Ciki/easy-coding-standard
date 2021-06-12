<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210612\Symfony\Component\DependencyInjection\Loader\Configurator;

use ECSPrefix20210612\Symfony\Component\DependencyInjection\Definition;
use ECSPrefix20210612\Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
abstract class AbstractServiceConfigurator extends \ECSPrefix20210612\Symfony\Component\DependencyInjection\Loader\Configurator\AbstractConfigurator
{
    protected $parent;
    protected $id;
    private $defaultTags = [];
    public function __construct(\ECSPrefix20210612\Symfony\Component\DependencyInjection\Loader\Configurator\ServicesConfigurator $parent, \ECSPrefix20210612\Symfony\Component\DependencyInjection\Definition $definition, string $id = null, array $defaultTags = [])
    {
        $this->parent = $parent;
        $this->definition = $definition;
        $this->id = $id;
        $this->defaultTags = $defaultTags;
    }
    public function __destruct()
    {
        // default tags should be added last
        foreach ($this->defaultTags as $name => $attributes) {
            foreach ($attributes as $attributes) {
                $this->definition->addTag($name, $attributes);
            }
        }
        $this->defaultTags = [];
    }
    /**
     * Registers a service.
     * @param string|null $id
     */
    public final function set($id, string $class = null) : \ECSPrefix20210612\Symfony\Component\DependencyInjection\Loader\Configurator\ServiceConfigurator
    {
        $this->__destruct();
        return $this->parent->set($id, $class);
    }
    /**
     * Creates an alias.
     */
    public final function alias(string $id, string $referencedId) : \ECSPrefix20210612\Symfony\Component\DependencyInjection\Loader\Configurator\AliasConfigurator
    {
        $this->__destruct();
        return $this->parent->alias($id, $referencedId);
    }
    /**
     * Registers a PSR-4 namespace using a glob pattern.
     */
    public final function load(string $namespace, string $resource) : \ECSPrefix20210612\Symfony\Component\DependencyInjection\Loader\Configurator\PrototypeConfigurator
    {
        $this->__destruct();
        return $this->parent->load($namespace, $resource);
    }
    /**
     * Gets an already defined service definition.
     *
     * @throws ServiceNotFoundException if the service definition does not exist
     */
    public final function get(string $id) : \ECSPrefix20210612\Symfony\Component\DependencyInjection\Loader\Configurator\ServiceConfigurator
    {
        $this->__destruct();
        return $this->parent->get($id);
    }
    /**
     * Removes an already defined service definition or alias.
     */
    public final function remove(string $id) : \ECSPrefix20210612\Symfony\Component\DependencyInjection\Loader\Configurator\ServicesConfigurator
    {
        $this->__destruct();
        return $this->parent->remove($id);
    }
    /**
     * Registers a stack of decorator services.
     *
     * @param InlineServiceConfigurator[]|ReferenceConfigurator[] $services
     */
    public final function stack(string $id, array $services) : \ECSPrefix20210612\Symfony\Component\DependencyInjection\Loader\Configurator\AliasConfigurator
    {
        $this->__destruct();
        return $this->parent->stack($id, $services);
    }
    /**
     * Registers a service.
     */
    public final function __invoke(string $id, string $class = null) : \ECSPrefix20210612\Symfony\Component\DependencyInjection\Loader\Configurator\ServiceConfigurator
    {
        $this->__destruct();
        return $this->parent->set($id, $class);
    }
}
