<?php

namespace spec\Im0rtality\ApiBundle\DataSource\Factory;

use Im0rtality\ApiBundle\DataSource\DataSourceInterface;
use Im0rtality\ApiBundle\DataSource\Factory\DataSourceFactory;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin DataSourceFactory
 */
class DataSourceFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Im0rtality\ApiBundle\DataSource\Factory\DataSourceFactory');
    }

    function it_should_create_parametrized_data_source(DataSourceInterface $dataSource)
    {
        $dataSource->setResource('foo')->willReturn($dataSource);
        $dataSource->getResource()->willReturn('foo');
        $this->setDataSource($dataSource);

        $this->create('foo')->getResource()->shouldBe('foo');
    }
}
