<?php

namespace Im0rtality\ApiBundle\Actions\Collection;

use Im0rtality\ApiBundle\Actions\ActionResult;
use Im0rtality\ApiBundle\DataSource\DataSourceInterface;
use Symfony\Component\HttpFoundation\Request;

class CountAction extends BaseCollectionAction
{
    const NAME = 'collection.count';

    /**
     * @inheritdoc
     */
    public function execute(Request $request, DataSourceInterface $resource)
    {
        return ActionResult::simple(200, ['count' => $resource->count()]);
    }
}
