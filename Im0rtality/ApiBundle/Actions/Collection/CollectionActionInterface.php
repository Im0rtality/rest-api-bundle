<?php

namespace Im0rtality\ApiBundle\Actions\Collection;

use Im0rtality\ApiBundle\Actions\ActionInterface;
use Im0rtality\ApiBundle\Actions\ActionResult;
use Im0rtality\ApiBundle\DataSource\DataSourceInterface;
use Symfony\Component\HttpFoundation\Request;

interface CollectionActionInterface extends ActionInterface
{
    /**
     * Performs the action
     *
     * @param Request             $request
     * @param DataSourceInterface $resource
     * @return ActionResult
     */
    public function execute(Request $request, DataSourceInterface $resource);
}
