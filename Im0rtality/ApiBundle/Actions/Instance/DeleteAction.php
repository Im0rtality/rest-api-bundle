<?php

namespace Im0rtality\ApiBundle\Actions\Instance;

use Im0rtality\ApiBundle\Actions\ActionResult;
use Im0rtality\ApiBundle\DataSource\DataSourceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DeleteAction extends BaseInstanceAction
{
    const NAME = 'instance.delete';

    /**
     * @inheritdoc
     */
    public function execute(Request $request, DataSourceInterface $resource, $identifier)
    {
        if (!$resource->delete(intval($identifier))) {
            throw new NotFoundHttpException();
        }

        return ActionResult::simple(200, ['status' => 'success']);
    }
}
