<?php

namespace Im0rtality\ApiBundle;

use Im0rtality\ApiBundle\DependencyInjection\Compiler\ActionPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class Im0rtalityApiBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ActionPass());
    }

}
