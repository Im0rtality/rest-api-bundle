<?php

namespace spec\Im0rtality\ApiBundle\Actions\Collection;

use Im0rtality\ApiBundle\Actions\ActionResult;
use Im0rtality\ApiBundle\DataSource\DataSourceInterface;
use Im0rtality\ApiBundle\EventListener\AuthorizationListener;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\Request;

/**
 * @mixin \Im0rtality\ApiBundle\Actions\Collection\CreateAction
 */
class CreateActionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Im0rtality\ApiBundle\Actions\Collection\CreateAction');
    }

    function it_should_create_via_dataSource(DataSourceInterface $dataSource)
    {
        $data = [
            'name' => 'foo'
        ];
        $request = Request::create('/user', 'POST');
        $request->attributes->set(AuthorizationListener::API_REQUEST_PAYLOAD, $data);
        $dataSource->create($data)->willReturn(array_merge($data, ['id' => 1]));

        $result = $this->execute($request, $dataSource, 1);

        $result->shouldHaveType('Im0rtality\ApiBundle\Actions\ActionResult');
        $result->getStatusCode()->shouldBe(201);
        $result->getResult()->shouldBe(array_merge($data, ['id' => 1]));
        $result->getType()->shouldBe(ActionResult::INSTANCE);
    }
}
