<?php

declare(strict_types = 1);

namespace App\Component\Doctrine\Common\DataFixtures;

use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Doctrine\Common\DataFixtures\SharedFixtureInterface;

/**
 * Abstract fixture class to extend from.
 *
 * @author Anton Pelykh <anton.pelykh.dev@gmail.com>
 */
abstract class AbstractFixture implements SharedFixtureInterface, ORMFixtureInterface
{
    /**
     * Fixture reference repository
     *
     * @var ReferenceRepository
     */
    protected $referenceRepository;

    /**
     * {@inheritdoc}
     */
    public function setReferenceRepository(ReferenceRepository $referenceRepository): void
    {
        $this->referenceRepository = $referenceRepository;
    }

    public function setReference(string $name, int $index, object $object): void
    {
        $this->referenceRepository->setReference($name.$index, $object);
    }

    public function addReference(string $name, int $index, object $object): void
    {
        $this->referenceRepository->addReference($name.$index, $object);
    }

    public function getReference(string $name, int $index): object
    {
        return $this->referenceRepository->getReference($name.$index);
    }

    public function hasReference(string $name, int $index): bool
    {
        return $this->referenceRepository->hasReference($name.$index);
    }
}
