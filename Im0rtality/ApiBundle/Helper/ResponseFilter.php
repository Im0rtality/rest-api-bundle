<?php

namespace Im0rtality\ApiBundle\Helper;

use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Im0rtality\ApiBundle\Actions\ActionResult;
use Im0rtality\ApiBundle\Security\PermissionManager;

class ResponseFilter
{
    /** @var  SerializerInterface */
    private $serializer;

    /** @var  PermissionManager */
    private $permissions;

    /**
     * @param PermissionManager $permissions
     */
    public function setPermissions(PermissionManager $permissions)
    {
        $this->permissions = $permissions;
    }

    /**
     * @param SerializerInterface $serializer
     */
    public function setSerializer(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param ActionResult $result
     * @param string       $resource
     * @param string       $action
     * @param string[]     $roles
     * @return array
     */
    public function filterResponse(ActionResult $result, $resource, $action, $roles)
    {
        $serializeAndFilter = function ($item) use ($resource, $action, $roles) {
            $data = $this->serializer->deserialize(
                $this->serializer->serialize($item, 'json', SerializationContext::create()->enableMaxDepthChecks()),
                'array',
                'json'
            );

            $fields = array_filter(
                array_keys($data),
                function ($field) use ($resource, $action, $roles) {
                    return $this->permissions->isReadable($resource . ':' . $field, $action, $roles);
                }
            );

            return array_intersect_key($data, array_flip($fields));
        };

        switch ($result->getType()) {
            case ActionResult::SIMPLE:
            case ActionResult::INSTANCE:
                $data = $serializeAndFilter($result->getResult());
                break;
            case ActionResult::COLLECTION:
                $data = array_map($serializeAndFilter, $result->getResult());
                break;
            default:
                throw new \InvalidArgumentException('Unsupported ActionResult type ' . $result->getType());
        }

        return $data;
    }
}
