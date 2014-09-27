<?php

namespace spec\Im0rtality\ApiBundle\DataSource;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Query;
use Doctrine\ORM\UnitOfWork;
use Im0rtality\ApiBundle\DataSource\ClassFactory;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin \Im0rtality\ApiBundle\DataSource\DoctrineOrmSource
 */
class DoctrineOrmSourceSpec extends ObjectBehavior
{
    function let()
    {
        $this->setResource('foo');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Im0rtality\ApiBundle\DataSource\DoctrineOrmSource');
    }

    function it_should_read_entity(ManagerRegistry $registry, EntityManager $em, ObjectRepository $repo)
    {
        $repo->find(1)->willReturn(['foo' => 1]);
        $em->getRepository('foo')->willReturn($repo);
        $registry->getManager(null)->willReturn($em);

        $this->setRegistry($registry);
        $this->read(1)->shouldBe(['foo' => 1]);
    }

    function it_should_index_entity(ManagerRegistry $registry, EntityManager $em, ObjectRepository $repo)
    {
        $repo->findBy([], ['id' => 'DESC'], 1, 2)->willReturn([1, 2, 3]);
        $em->getRepository('foo')->willReturn($repo);
        $registry->getManager(null)->willReturn($em);

        $this->setRegistry($registry);
        $this->index(1, 2, 'id', 'DESC')->shouldBe([1, 2, 3]);
    }

    function it_should_count_entities(ManagerRegistry $registry, EntityManager $em, AbstractQuery $query)
    {
        $query->getSingleScalarResult()->willReturn(42);
        $em->createQuery('SELECT COUNT(r) FROM foo r')->willReturn($query);
        $registry->getManager(null)->willReturn($em);

        $this->setRegistry($registry);
        $this->count()->shouldBe(42);
    }

    function it_should_delete_entity(ManagerRegistry $registry, EntityManager $em)
    {
        $em->getPartialReference('foo', 1)->willReturn(42);
        $em->remove(42)->shouldBeCalled();
        $em->flush()->shouldBeCalled();
        $registry->getManager(null)->willReturn($em);

        $this->setRegistry($registry);
        $this->delete(1)->shouldBe(true);
    }

    function it_should_create_entity(
        ManagerRegistry $registry,
        EntityManager $em,
        ClassFactory $factory,
        ObjectRepository $repository,
        ClassMetadataInfo $class
    ) {
        $entity = (object)['foo' => null, 'bar' => null];

        $class->getFieldNames()->willReturn(['id', 'foo', 'bar']);
        $class->setFieldValue($entity, 'id', null)->shouldBeCalled();
        $class->setFieldValue($entity, 'foo', 1)->shouldBeCalled();
        $class->setFieldValue($entity, 'bar', 2)->shouldBeCalled();

        $class->getAssociationNames()->willReturn([]);

        $repository->getClassName()->willReturn('foo');
        $factory->create('foo')->willReturn($entity);
        $em->getRepository('foo')->willReturn($repository);
        $em->getClassMetadata('foo')->willReturn($class);
        $em->persist($entity)->shouldBeCalled();
        $em->flush()->shouldBeCalled();
        $registry->getManager(null)->willReturn($em);

        $this->setRegistry($registry);
        $this->setClassFactory($factory);

        $entity->foo = 1;
        $entity->bar = 2;

        $this->create(['foo' => 1, 'bar' => 2])->shouldBe($entity);
    }

    function it_should_update_entity(
        ManagerRegistry $registry,
        EntityManager $em,
        ObjectRepository $repo,
        ClassMetadataInfo $class
    ) {
        $entity = (object)['id' => 1, 'foo' => 1, 'bar' => 2];

        $class->getFieldNames()->willReturn(['id', 'foo', 'bar']);
        $class->getAssociationNames()->willReturn([]);
        $class->setFieldValue($entity, 'foo', 1)->shouldBeCalled();
        $class->setFieldValue($entity, 'bar', 2)->shouldBeCalled();

        $em->persist($entity)->shouldBeCalled();
        $em->flush()->shouldBeCalled();
        $em->getClassMetadata('foo')->willReturn($class);
        $registry->getManager(null)->willReturn($em);
        $em->getRepository('foo')->willReturn($repo);
        $repo->find(1)->willReturn($entity);

        $this->setRegistry($registry);
        $this->update(1, ['foo' => 1, 'bar' => 2])->shouldBeKindaSame((object)['id' => 1, 'foo' => 1, 'bar' => 2]);
    }

    function it_should_perform_search_query(ManagerRegistry $registry, EntityManager $em, EntityRepository $repo)
    {
        $query = ['foo' => 'bar'];
        $result = [['id' => 1, 'foo' => 'bar']];

        $repo->findBy($query)->willReturn($result);
        $em->getRepository('foo')->willReturn($repo);
        $registry->getManager(null)->willReturn($em);

        $this->setRegistry($registry);
        $this->query($query)->shouldBe($result);
    }

    function getMatchers()
    {
        return [
            'beKindaSame' => function ($actual, $expected) {
                return serialize($actual) == serialize($expected);
            }
        ];
    }
}
