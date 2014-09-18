<?php

namespace spec\Im0rtality\ApiBundle\EventListener;

use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerInterface;
use Im0rtality\ApiBundle\Actions\ActionInterface;
use Im0rtality\ApiBundle\Actions\ActionManager;
use Im0rtality\ApiBundle\Controller\ApiControllerInterface;
use Im0rtality\ApiBundle\EventListener\AuthorizationListener;
use Im0rtality\ApiBundle\Helper\ResourceResolver;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

/**
 * @mixin AuthorizationListener
 */
class AuthorizationListenerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Im0rtality\ApiBundle\EventListener\AuthorizationListener');
    }

    function it_should_recognize_and_mark_api_requests(
        ApiControllerInterface $controller,
        FilterControllerEvent $event,
        ResourceResolver $resourceResolver,
        ActionManager $actionResolver,
        SerializerInterface $serializer,
        ActionInterface $action
    ) {
        $payload = ['foo' => 'bar', 'baz' => 'qux'];
        $resource = 'baz';
        $fqcn = 'BarBundle/Entity/Bar';
        $actionName = 'fooAction';
        $httpMethod = 'GET';
        $identifier = null;

        $request = new Request(
            [], [], [
                'actionName' => $actionName,
                'resource'   => 'baz',
                'identifier' => $identifier,
            ], [], [], [], json_encode($payload)
        );

        $resourceResolver->resolve($resource)->willReturn($fqcn);
        $actionResolver->resolve($fqcn, $actionName, $httpMethod, !is_null($identifier))->willReturn($action);

        $event->getRequest()->willReturn($request);
        $event->getController()->willReturn([$controller]);

        $this->setSerializer($serializer);
        $this->setResourceResolver($resourceResolver);
        $this->setActionResolver($actionResolver);

        $this->populateRequestParams($event);
    }
}
