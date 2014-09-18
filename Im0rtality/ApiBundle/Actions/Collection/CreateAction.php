<?php

namespace Im0rtality\ApiBundle\Actions\Collection;

use Im0rtality\ApiBundle\Actions\ActionResult;
use Im0rtality\ApiBundle\DataSource\DataSourceInterface;
use Im0rtality\ApiBundle\EventListener\AuthorizationListener;
use Symfony\Component\HttpFoundation\Request;

class CreateAction extends BaseCollectionAction
{
    const NAME = 'collection.create';

    /**
     * @inheritdoc
     */
    public function execute(Request $request, DataSourceInterface $resource)
    {
        return ActionResult::instance(
            201,
            $resource->create($request->attributes->get(AuthorizationListener::API_REQUEST_PAYLOAD))
        );
    }
}
