<?php

namespace Im0rtality\ApiBundle\Controller;

use Im0rtality\ApiBundle\Actions\ActionInterface;
use Im0rtality\ApiBundle\Actions\ActionResult;
use Im0rtality\ApiBundle\Actions\Collection\CollectionActionInterface;
use Im0rtality\ApiBundle\Actions\Instance\InstanceActionInterface;
use Im0rtality\ApiBundle\DataSource\Factory\DataSourceFactory;
use Im0rtality\ApiBundle\Helper\OwnershipResolver;
use Im0rtality\ApiBundle\Helper\RequestValidator;
use Im0rtality\ApiBundle\Helper\ResponseFilter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Role\RoleInterface;

class ApiController extends Controller implements ApiControllerInterface
{
    /**
     * @param Request         $request
     * @param string          $resource
     * @param ActionInterface $action
     * @param mixed|null      $payload
     * @param string|null     $identifier
     * @return Response
     */
    public function customAction(Request $request, $resource, ActionInterface $action, $payload, $identifier = null)
    {
        $token = $this->get('security.context')->getToken();
        $roles = $this->stringifyRoles(
            $this->get('security.role_hierarchy')->getReachableRoles(
                $token->getRoles()
            )
        );

        /** @var DataSourceFactory $factory */
        $factory = $this->container->get('im0rtality_api.data_source.factory');

        $roles = $this->handleObjectOwnership($resource, $identifier, $factory, $token, $roles);

        /** @var RequestValidator $requestValidator */
        $requestValidator = $this->container->get('im0rtality_api.api.security.request_validator');
        $requestValidator->validate($resource, $action->getName(), $payload, $roles);

        $result = $this->execute($request, $resource, $action, $identifier, $factory);

        /** @var ResponseFilter $responseFilter */
        $responseFilter = $this->container->get('im0rtality_api.api.security.response_filter');

        return new Response(
            $this->renderView(
                'Im0rtalityApiBundle:Api:custom.json.twig',
                ['data' => $responseFilter->filterResponse($result, $resource, $action->getName(), $roles)]
            ),
            $result->getStatusCode()
        );
    }

    /**
     * @param RoleInterface[] $roles
     * @return string[]
     */
    private function stringifyRoles($roles)
    {
        return array_map(
            function (RoleInterface $role) {
                return $role->getRole();
            },
            $roles
        );
    }

    /**
     * @param Request           $request
     * @param string            $resource
     * @param ActionInterface   $action
     * @param string|null       $identifier
     * @param DataSourceFactory $factory
     * @return ActionResult
     */
    private function execute(Request $request, $resource, ActionInterface $action, $identifier, $factory)
    {
        switch (true) {
            case ($action instanceof CollectionActionInterface):
                $result = $action->execute($request, $factory->create($resource));
                break;
            case ($action instanceof InstanceActionInterface):
                $result = $action->execute($request, $factory->create($resource), $identifier);
                break;
            default:
                throw new \InvalidArgumentException(
                    'Action must implement CollectionActionInterface or InstanceActionInterface'
                );
        }
        return $result;
    }

    /**
     * @param string            $resource
     * @param string|null       $identifier
     * @param DataSourceFactory $factory
     * @param TokenInterface    $token
     * @param string[]            $roles
     * @return string[]
     */
    private function handleObjectOwnership($resource, $identifier, $factory, $token, $roles)
    {
        if ($identifier && is_object($token->getUser())) {
            /** @var OwnershipResolver $ownershipResolver */
            $ownershipResolver = $this->container->get('im0rtality_api.api.security.ownership_resolver');

            $instance = $factory->create($resource)->read($identifier);
            $isOwner = $ownershipResolver->resolve($token->getUser()->getId(), $instance);
            if ($isOwner) {
                $roles[] = 'ROLE_OWNER';
                return $roles;
            }
            return $roles;
        }
        return $roles;
    }
}
