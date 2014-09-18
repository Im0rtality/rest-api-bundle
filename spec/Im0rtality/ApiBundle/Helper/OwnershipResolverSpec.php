<?php

namespace spec\Im0rtality\ApiBundle\Helper;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin \Im0rtality\ApiBundle\Helper\OwnershipResolver
 */
class OwnershipResolverSpec extends ObjectBehavior
{
    function let()
    {
        $ownerships = [
            'stdClass' => 'id'
        ];
        $this->setOwnerships($ownerships);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Im0rtality\ApiBundle\Helper\OwnershipResolver');
    }

    function it_resolve_ownership()
    {
        $this->resolve(1, (object)['id' => 1])->shouldBe(true);
        $this->resolve(1, (object)['id' => 2])->shouldBe(false);
    }
}
