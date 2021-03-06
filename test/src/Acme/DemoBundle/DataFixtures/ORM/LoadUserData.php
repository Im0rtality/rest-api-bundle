<?php

namespace Acme\ApiBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Acme\DemoBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

class LoadUserData implements FixtureInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create();

        $userAdmin = new User();
        $userAdmin->setUsername('admin');
        $this->setPassword($userAdmin, 'admin');
        $userAdmin->setEmail('admin@admin.com');
        $userAdmin->setName('The Admin');
        $userAdmin->setLastname('');
        $userAdmin->setRoles(['ROLE_ADMIN']);
        $userAdmin->setEnabled(true);
        $userAdmin->setImage('admin.png');

        $manager->persist($userAdmin);

        $user = new User();
        $user->setUsername('user');
        $this->setPassword($user, 'user');
        $user->setEmail('user@user.com');
        $user->setLastname('');
        $user->setName('The User');
        $user->setRoles(['ROLE_USER']);
        $user->setEnabled(true);
        $user->setImage('user.png');

        $manager->persist($user);

        for ($i = 0; $i < 9; $i++) {
            $user = new User();
            $user->setUsername($faker->userName);
            $this->setPassword($user, 'secret');
            $user->setEmail($faker->companyEmail);
            $user->setName($faker->firstName);
            $user->setLastname($faker->lastName);
            $user->setRoles(['ROLE_USER']);
            $user->setEnabled(true);
            $user->setImage('user.png');

            $manager->persist($user);
        }
        $manager->flush();
    }

    /**
     * @param User   $userAdmin
     * @param string $password
     */
    private function setPassword(User $userAdmin, $password)
    {
        /** @var PasswordEncoderInterface $encoder */
        $encoder = $this->container
            ->get('security.encoder_factory')
            ->getEncoder($userAdmin);
        $userAdmin->setPassword($encoder->encodePassword($password, $userAdmin->getSalt()));
    }
}
