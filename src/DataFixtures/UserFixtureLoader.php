<?php

declare(strict_types = 1);

namespace App\DataFixtures;

use App\Component\Doctrine\Common\DataFixtures\AbstractFixture;
use App\Component\Security\Core\User\UserInterface;
use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class UserFixtureLoader extends AbstractFixture
{
    public const REF_ENABLED_ADMIN = 'ENABLED_ADMIN_USER';
    public const REF_ENABLED_INTERVIEWER = 'ENABLED_INTERVIEWER_USER';
    public const REF_LOCKED_INTERVIEWER = 'LOCKED_INTERVIEWER_USER';
    public const REF_UNVERIFIED_INTERVIEWER = 'UNVERIFIED_INTERVIEWER_USER';
    /**
     * @var EncoderFactoryInterface
     */
    private $encoderFactory;

    /**
     * UserFixtureLoader constructor.
     *
     * @param EncoderFactoryInterface $encoderFactory
     */
    public function __construct(EncoderFactoryInterface $encoderFactory)
    {
        $this->encoderFactory = $encoderFactory;
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
        $passwordEncoder = $this->encoderFactory->getEncoder(UserInterface::class);

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
                    $user
                        ->setStatus(User::STATUS_ENABLED)
                        ->setRole(User::ROLE_ADMIN);
                    break;
                case self::REF_ENABLED_INTERVIEWER:
                    $user
                        ->setStatus(User::STATUS_ENABLED)
                        ->setRole(User::ROLE_INTERVIEWER);
                    break;
                case self::REF_LOCKED_INTERVIEWER:
                    $user
                        ->setStatus(User::STATUS_LOCKED)
                        ->setRole(User::ROLE_INTERVIEWER);
                    break;
                case self::REF_UNVERIFIED_INTERVIEWER:
                    $user
                        ->setStatus(User::STATUS_UNVERIFIED)
                        ->setRole(User::ROLE_INTERVIEWER);
                    break;
            }

            $manager->persist($user);

            $this->addReference($ref, $i, $user);
        }

        $manager->flush();
    }
}
