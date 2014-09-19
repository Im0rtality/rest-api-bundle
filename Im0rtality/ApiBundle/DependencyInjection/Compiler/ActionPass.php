<?php

namespace Im0rtality\ApiBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class ActionPass implements CompilerPassInterface
{

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container)
    {
        $collectionActions = $container->findTaggedServiceIds('im0rtality_api.api.action.collection');
        $instanceActions = $container->findTaggedServiceIds('im0rtality_api.api.action.instance');

        $actionManager = $container->getDefinition('im0rtality_api.action.manager');

        $this->addActions($container, $collectionActions, $actionManager, 'addCollectionAction');
        $this->addActions($container, $instanceActions, $actionManager, 'addInstanceAction');

        $container->setDefinition('im0rtality_api.action.manager', $actionManager);
    }

    /**
     * @param ContainerBuilder $container
     * @param                  $collectionActions
     * @param  Definition      $actionManager
     * @param  string          $method
     */
    private function addActions(ContainerBuilder $container, $collectionActions, $actionManager, $method)
    {
        foreach ($collectionActions as $service => $tags) {
            $tag = current($tags);
            $actionManager->addMethodCall(
                $method,
                [$container->getDefinition($service), $tag['resource'], @$tag['action'], $tag['method']]
            );
        }
    }
}
