<?php

namespace Im0rtality\ApiBundle\EventListener;

use JMS\Serializer\SerializerInterface;
use Im0rtality\ApiBundle\Actions\ActionManager;
use Im0rtality\ApiBundle\Controller\ApiControllerInterface;
use Im0rtality\ApiBundle\Helper\ResourceResolver;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class AuthorizationListener implements EventSubscriberInterface
{
    const API_REQUEST_RESOURCE = 'resource';
    const API_REQUEST_ACTION = 'action';
    const API_REQUEST_PAYLOAD = 'payload';

    /** @var  ResourceResolver */
    protected $resourceResolver;

    /** @var  ActionManager */
    protected $actionResolver;

    /** @var  SerializerInterface */
    protected $serializer;

    /**
     * @param ActionManager $actionResolver
     */
    public function setActionResolver(ActionManager $actionResolver)
    {
        $this->actionResolver = $actionResolver;
    }

    /**
     * @param ResourceResolver $resourceResolver
     */
    public function setResourceResolver(ResourceResolver $resourceResolver)
    {
        $this->resourceResolver = $resourceResolver;
    }

    /**
     * @param SerializerInterface $serializer
     */
    public function setSerializer(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function populateRequestParams(FilterControllerEvent $event)
    {
        if ($event->getController()[0] instanceof ApiControllerInterface) {
            $request = $event->getRequest();

            $resource = $this->resourceResolver->resolve($request->attributes->get('resource'));
            $action = $this->actionResolver->resolve(
                $resource,
                $request->attributes->get('actionName'),
                $request->getMethod(),
                null !== $request->attributes->get('identifier')
            );

            $content = $request->getContent();
            $payload = empty($content) ? null : $this->serializer->deserialize(
                $content,
                'array',
                'json'
            );

            $request->attributes->set(self::API_REQUEST_RESOURCE, $resource);
            $request->attributes->set(self::API_REQUEST_ACTION, $action);
            $request->attributes->set(self::API_REQUEST_PAYLOAD, $payload);
        }
    }

    /**
     * @inheritdoc
     * @codeCoverageIgnore
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => [
                ['populateRequestParams', 30],
            ],
        ];
    }
}
