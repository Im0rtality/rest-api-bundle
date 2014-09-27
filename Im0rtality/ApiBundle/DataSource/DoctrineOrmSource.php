<?php

namespace Im0rtality\ApiBundle\DataSource;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\UnitOfWork;

class DoctrineOrmSource implements DataSourceInterface
{
    /** @var  ClassFactory */
    protected $classFactory;

    /** @var  ManagerRegistry */
    protected $registry;

    /** @var  string|null */
    protected $managerName;

    /** @var  string */
    protected $resource;

    /**
     * @param ManagerRegistry $registry
     */
    public function setRegistry(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param null|string $managerName
     */
    public function setManagerName($managerName)
    {
        $this->managerName = $managerName;
    }

    /**
     * @inheritdoc
     */
    public function index($limit = null, $offset = null, $orderBy = null, $order = null)
    {
        $sort = $orderBy && $order ? [$orderBy => $order] : null;

        return $this->getManager()->getRepository($this->resource)->findBy([], $sort, $limit, $offset);
    }

    /**
     * @inheritdoc
     */
    public function read($identifier)
    {
        return $this->getManager()->getRepository($this->resource)->find($identifier);
    }

    /**
     * @inheritdoc
     */
    public function update($identifier, $patch)
    {
        $object = $this->populate(array_replace($patch, ['id' => $identifier]));

        $this->persist($object);

        return $object;
    }

    /**
     * @inheritdoc
     */
    public function delete($identifier)
    {
        $entityManager = $this->getManager();

        $object = $entityManager->getPartialReference($this->resource, $identifier);
        $entityManager->remove($object);
        $entityManager->flush();
        return true;
    }

    /**
     * @inheritdoc
     */
    public function create($data)
    {
        $data['id'] = null;

        $object = $this->populate($data);
        $this->persist($object, $data);

        return $object;
    }

    /**
     * @inheritdoc
     */
    public function count()
    {
        $query = "SELECT COUNT(r) FROM {$this->resource} r";
        return $this->getManager()->createQuery($query)->getSingleScalarResult();
    }

    /**
     * Returns elements that matches the query
     *
     * @param mixed $query
     * @return array
     */
    public function query($query)
    {
        return $this->getManager()->getRepository($this->resource)->findBy($query);
    }

    /**
     * @inheritdoc
     */
    public function setResource($resource)
    {
        $this->resource = $resource;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getResource()
    {
        return $this->resource;
    }

    public function setClassFactory(ClassFactory $factory)
    {
        $this->classFactory = $factory;
    }

    /**
     * @param $object
     * @param $data
     */
    private function persist($object)
    {
        $entityManager = $this->getManager();
        $entityManager->persist($object);
        $entityManager->flush();
    }

    /**
     * @return EntityManager
     * @throws \InvalidArgumentException
     */
    private function getManager()
    {
        $manager = $this->registry->getManager($this->managerName);
        if (!$manager instanceof EntityManager) {
            throw new \InvalidArgumentException('DoctrineOrmSource only supports ORM EntityManager as $manager');
        }
        return $manager;
    }

    /**
     * Returns underlying driver so you can do "things" at price of sacrificing abstraction
     *
     * @return EntityManager
     */
    public function getDriver()
    {
        return $this->getManager();
    }

    /**
     * @param $data
     * @return object
     */
    private function populate($data)
    {
        $object = $this->getManager()->getUnitOfWork()->createEntity($this->resource, $data);
        $this->getManager()->detach($object);
        return $object;
    }
}
