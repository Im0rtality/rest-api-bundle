<?php

namespace Im0rtality\ApiBundle\Actions\Instance;

use Im0rtality\ApiBundle\Actions\ActionResult;
use Im0rtality\ApiBundle\DataSource\DataSourceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ReadAction extends BaseInstanceAction
{
    const NAME = 'instance.read';

    /**
     * @inheritdoc
     */
    public function execute(Request $request, DataSourceInterface $resource, $identifier)
    {
        $instance = $resource->read($identifier);
        if (is_null($instance)) {
            throw new NotFoundHttpException();
        }

        return ActionResult::instance(200, $instance);
    }
}
