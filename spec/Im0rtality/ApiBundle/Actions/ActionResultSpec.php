<?php

namespace spec\Im0rtality\ApiBundle\Actions;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin \Im0rtality\ApiBundle\Actions\ActionResult
 */
class ActionResultSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Im0rtality\ApiBundle\Actions\ActionResult');
    }
}
