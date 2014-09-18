<?php

namespace Im0rtality\ApiBundle\Actions\Collection;

use Im0rtality\ApiBundle\Actions\ActionResult;
use Im0rtality\ApiBundle\DataSource\DataSourceInterface;
use Symfony\Component\HttpFoundation\Request;

class IndexAction extends BaseCollectionAction
{
    const NAME = 'collection.index';

    /**
     * @inheritdoc
     */
    public function execute(Request $request, DataSourceInterface $resource)
    {
        return ActionResult::collection(
            200,
            $resource->index(
                $request->query->get('limit'),
                $request->query->get('offset')
            )
        );
    }
}
