<?php

namespace Im0rtality\ApiBundle\Actions;

abstract class BaseAction implements ActionInterface
{
    const NAME = null;

    /**
     * @return string
     */
    public function getName()
    {
        return static::NAME;
    }
}
