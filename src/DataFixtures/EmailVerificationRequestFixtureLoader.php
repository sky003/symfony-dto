<?php

declare(strict_types = 1);

namespace App\DataFixtures;

use App\Component\Doctrine\Common\DataFixtures\AbstractFixture;
use App\Entity\EmailVerificationRequest;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class EmailVerificationRequestFixtureLoader extends AbstractFixture implements DependentFixtureInterface
{
    public const REF_PENDING_REQUEST = 'PENDING_REQUEST';
    public const REF_EXPIRED_REQUEST = 'EXPIRED_REQUEST';

    /**
     * {@inheritdoc}
     */
    public function getDependencies(): array
    {
        return [
            UserFixtureLoader::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $this->loadWithReference($manager, self::REF_PENDING_REQUEST);
        $this->loadWithReference($manager, self::REF_EXPIRED_REQUEST);
    }

    private function loadWithReference(ObjectManager $manager, string $ref): void
    {
        $faker = \Faker\Factory::create();

        for ($i = 0; $i < 5; $i++) {
            $token = bin2hex(random_bytes(64));

            $verificationRequest = new EmailVerificationRequest();
            $verificationRequest
                ->setToken($token)
                ->setCreatedAt($faker->dateTimeBetween('-30 days', 'now'))
                ->setUpdatedAt($faker->dateTimeBetween($verificationRequest->getCreatedAt(), 'now'));
            switch($ref) {
                case self::REF_PENDING_REQUEST:
                    /** @var User $user */
                    $user = $this->getReference(UserFixtureLoader::REF_UNVERIFIED_INTERVIEWER, $i);

                    $verificationRequest
                        ->setUser($user)
                        ->setStatus(EmailVerificationRequest::STATUS_PENDING)
                        ->setExpiresAt(new \DateTime('now + 2 hours'));
                    break;
                case self::REF_EXPIRED_REQUEST:
                    /** @var User $user */
                    $user = $this->getReference(UserFixtureLoader::REF_UNVERIFIED_INTERVIEWER, $i);

                    $verificationRequest
                        ->setUser($user)
                        ->setStatus(EmailVerificationRequest::STATUS_PENDING)
                        ->setExpiresAt(new \DateTime('now - 2 hours'));
                    break;
            }

            $manager->persist($verificationRequest);

            $this->addReference($ref, $i, $verificationRequest);
        }

        $manager->flush();
    }
}
