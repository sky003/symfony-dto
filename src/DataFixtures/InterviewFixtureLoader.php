<?php

declare(strict_types = 1);

namespace App\DataFixtures;

use App\Component\Doctrine\Common\DataFixtures\AbstractFixture;
use App\Entity\Interview;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class InterviewFixtureLoader extends AbstractFixture implements DependentFixtureInterface
{
    public const REF_ENABLED_INTERVIEW = 'ENABLED_INTERVIEW';

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
        $this->loadWithReference($manager, self::REF_ENABLED_INTERVIEW);
    }

    private function loadWithReference(ObjectManager $manager, string $ref): void
    {
        $faker = \Faker\Factory::create();

        for ($i = 0; $i < 5; $i++) {
            $interview = new Interview();
            $interview
                ->setName($faker->sentence(10))
                ->setIntro($faker->optional()->text(1000))
                ->setCreatedAt($faker->dateTimeBetween('-30 days', 'now'))
                ->setUpdatedAt($faker->dateTimeBetween($interview->getCreatedAt(), 'now'));
            switch ($ref) {
                case self::REF_ENABLED_INTERVIEW:
                    /** @var User $user */
                    $user = $this->getReference(UserFixtureLoader::REF_ENABLED_INTERVIEWER, $i);

                    $interview
                        ->setUser($user);
                    break;
            }

            $manager->persist($interview);

            $this->addReference($ref, $i, $interview);
        }

        $manager->flush();
    }
}
