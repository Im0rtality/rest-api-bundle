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
        $collectionActions = $container->findTaggedServiceIds('api_bundle.api.action.collection');
        $instanceActions = $container->findTaggedServiceIds('api_bundle.api.action.instance');

        $actionManager = $container->getDefinition('api_bundle.action.manager');

        $this->addActions($container, $collectionActions, $actionManager, 'addCollectionAction');
        $this->addActions($container, $instanceActions, $actionManager, 'addInstanceAction');

        $container->setDefinition('api_bundle.action.manager', $actionManager);
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
