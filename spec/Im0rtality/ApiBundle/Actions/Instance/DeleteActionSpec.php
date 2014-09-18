<?php

namespace spec\Im0rtality\ApiBundle\Actions\Instance;

use Im0rtality\ApiBundle\Actions\ActionResult;
use Im0rtality\ApiBundle\DataSource\DataSourceInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\Request;

/**
 * @mixin \Im0rtality\ApiBundle\Actions\Instance\DeleteAction
 */
class DeleteActionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Im0rtality\ApiBundle\Actions\Instance\DeleteAction');
    }

    function it_should_delete_via_dataSource(DataSourceInterface $dataSource)
    {
        $request = Request::create('/user/1', 'DELETE');
        $dataSource->delete(1)->willReturn(true);

        $result = $this->execute($request, $dataSource, 1);

        $result->shouldHaveType('Im0rtality\ApiBundle\Actions\ActionResult');
        $result->getStatusCode()->shouldBe(200);
        $result->getResult()->shouldBe(['status' => 'success']);
        $result->getType()->shouldBe(ActionResult::SIMPLE);
    }

    function it_should_throw_if_instance_not_found(DataSourceInterface $dataSource)
    {
        $request = Request::create('/user/1', 'DELETE');

        $dataSource->delete(1)->willReturn(false);

        $this->shouldThrow('Symfony\Component\HttpKernel\Exception\NotFoundHttpException')
            ->during('execute', [$request, $dataSource, 1]);
    }
}
