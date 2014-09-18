<?php

namespace spec\Im0rtality\ApiBundle\Actions\Instance;

use Im0rtality\ApiBundle\Actions\ActionResult;
use Im0rtality\ApiBundle\DataSource\DataSourceInterface;
use Im0rtality\ApiBundle\EventListener\AuthorizationListener;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\Request;

/**
 * @mixin \Im0rtality\ApiBundle\Actions\Instance\UpdateAction
 */
class UpdateActionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Im0rtality\ApiBundle\Actions\Instance\UpdateAction');
    }

    function it_should_update_via_dataSource(DataSourceInterface $dataSource)
    {
        $full = ['id' => 1];
        $data = ['name' => 'foo'];
        $request = Request::create('/user/1', 'PUT');
        $request->attributes->set(AuthorizationListener::API_REQUEST_PAYLOAD, $data);
        $dataSource->update(1, $data)->willReturn(true);
        $dataSource->read(1)->willReturn(array_merge($full, $data));

        $result = $this->execute($request, $dataSource, 1);

        $result->shouldHaveType('Im0rtality\ApiBundle\Actions\ActionResult');
        $result->getStatusCode()->shouldBe(200);
        $result->getResult()->shouldBe(array_merge($full, $data));
        $result->getType()->shouldBe(ActionResult::INSTANCE);
    }

    function it_should_throw_if_instance_not_found(DataSourceInterface $dataSource)
    {
        $data = ['name' => 'foo'];
        $request = Request::create('/user/1', 'PUT');
        $request->attributes->set(AuthorizationListener::API_REQUEST_PAYLOAD, $data);
        $dataSource->update(1, $data)->willReturn(null);

        $this->shouldThrow('Symfony\Component\HttpKernel\Exception\NotFoundHttpException')
            ->during('execute', [$request, $dataSource, 1]);
    }
}
