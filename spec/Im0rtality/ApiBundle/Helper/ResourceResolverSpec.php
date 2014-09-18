<?php

namespace spec\Im0rtality\ApiBundle\Helper;

use Im0rtality\ApiBundle\Helper\ResourceResolver;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin ResourceResolver
 */
class ResourceResolverSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Im0rtality\ApiBundle\Helper\ResourceResolver');
    }

    function it_should_resolve_resource_to_entity_name()
    {
        $this->setResourceMapping(['foo' => 'BarBundle:Foo']);
        $this->resolve('foo')->shouldBe('BarBundle:Foo');
    }
}
