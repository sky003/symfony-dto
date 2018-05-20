<?php

declare(strict_types = 1);

namespace App\DataFixtures;

use App\Component\Doctrine\Common\DataFixtures\AbstractFixture;
use App\Component\Security\Core\User\UserInterface;
use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

class UserFixtureLoader extends AbstractFixture implements ContainerAwareInterface
{
    public const REF_ENABLED_ADMIN = 'ENABLED_ADMIN_USER';
    public const REF_ENABLED_INTERVIEWER = 'ENABLED_INTERVIEWER_USER';
    public const REF_LOCKED_INTERVIEWER = 'LOCKED_INTERVIEWER_USER';
    public const REF_UNVERIFIED_INTERVIEWER = 'UNVERIFIED_INTERVIEWER_USER';

    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null): void
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $this->loadWithReference($manager, self::REF_ENABLED_ADMIN);
        $this->loadWithReference($manager, self::REF_ENABLED_INTERVIEWER);
        $this->loadWithReference($manager, self::REF_LOCKED_INTERVIEWER);
        $this->loadWithReference($manager, self::REF_UNVERIFIED_INTERVIEWER);
    }

    private function loadWithReference(ObjectManager $manager, string $ref): void
    {
        $faker = \Faker\Factory::create();
        /** @var PasswordEncoderInterface $passwordEncoder */
        $passwordEncoder = $this->container->get('test.security.encoder_factory')->getEncoder(UserInterface::class);

        for ($i = 0; $i < 5; $i++) {
            $user = new User();
            $user
                ->setEmail($faker->email)
                ->setPasswordHash(
                    $passwordEncoder->encodePassword('password'.$i, null)
                )
                ->setCreatedAt($faker->dateTimeBetween('-30 days', 'now'))
                ->setUpdatedAt($faker->dateTimeBetween($user->getCreatedAt(), 'now'));
            switch ($ref) {
                case self::REF_ENABLED_ADMIN:
                    $user->setStatus(User::STATUS_ENABLED);
                    $user->setRole(User::ROLE_ADMIN);
                    break;
                case self::REF_ENABLED_INTERVIEWER:
                    $user->setStatus(User::STATUS_ENABLED);
                    $user->setRole(User::ROLE_INTERVIEWER);
                    break;
                case self::REF_LOCKED_INTERVIEWER:
                    $user->setStatus(User::STATUS_LOCKED);
                    $user->setRole(User::ROLE_INTERVIEWER);
                    break;
                case self::REF_UNVERIFIED_INTERVIEWER:
                    $user->setStatus(User::STATUS_UNVERIFIED);
                    $user->setRole(User::ROLE_INTERVIEWER);
                    break;
            }

            $manager->persist($user);

            $this->addReference($ref, $i, $user);
        }

        $manager->flush();
    }
}
