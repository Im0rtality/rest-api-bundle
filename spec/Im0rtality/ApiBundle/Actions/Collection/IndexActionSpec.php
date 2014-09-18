<?php

namespace spec\Im0rtality\ApiBundle\Actions\Collection;

use Im0rtality\ApiBundle\Actions\ActionResult;
use Im0rtality\ApiBundle\DataSource\DataSourceInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\Request;

/**
 * @mixin \Im0rtality\ApiBundle\Actions\Collection\IndexAction
 */
class IndexActionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Im0rtality\ApiBundle\Actions\Collection\IndexAction');
    }

    function it_should_index_via_dataSource(DataSourceInterface $dataSource)
    {
        $request = new Request(['limit' => 1, 'offset' => 1]);
        $dataSource->index(1, 1)->willReturn([]);

        $result = $this->execute($request, $dataSource);

        $result->shouldHaveType('Im0rtality\ApiBundle\Actions\ActionResult');
        $result->getStatusCode()->shouldBe(200);
        $result->getResult()->shouldBe([]);
        $result->getType()->shouldBe(ActionResult::COLLECTION);
    }
}
