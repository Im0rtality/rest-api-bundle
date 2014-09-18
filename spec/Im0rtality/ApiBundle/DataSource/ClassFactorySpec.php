<?php

namespace spec\Im0rtality\ApiBundle\DataSource;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ClassFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Im0rtality\ApiBundle\DataSource\ClassFactory');
    }

    function it_should_create_class()
    {
        $this->create('stdClass')->shouldHaveType('stdClass');
    }
}
