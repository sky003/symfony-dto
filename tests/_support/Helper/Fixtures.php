<?php

declare(strict_types = 1);

namespace Helper;

use Codeception\Module;
use Doctrine\Bundle\FixturesBundle\Loader\SymfonyFixturesLoader;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;

/**
 * Fixtures helper.
 *
 * @author Anton Pelykh <anton.pelykh.dev@gmail.com>
 */
class Fixtures extends Module
{
    /**
     * @param string $className
     *
     * @return \App\Component\Doctrine\Common\DataFixtures\AbstractFixture Loaded fixture instance
     *                                                                     (loaded by provided class name).
     * @throws \Codeception\Exception\ModuleException
     */
    public function loadFixture(string $className): FixtureInterface
    {
        /** @var Module\Symfony $symfony */
        $symfony = $this->getModule('Symfony');

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $symfony->_getContainer()->get('test.doctrine.orm.fixtures_entity_manager');

        $entityManager->getEventManager()->addEventListener('loadClassMetadata', new class() {
            /**
             * Event listener for class metadata loading.
             *
             * @param LoadClassMetadataEventArgs $eventArgs
             */
            public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs): void
            {
                /** @var \Doctrine\ORM\Mapping\ClassMetadata $metadata */
                $metadata = $eventArgs->getClassMetadata();
                // `Faker` generates all the fields for fixtures, including `createdAt` and `updatedAt` values.
                // So the lifecycle callbacks should be disabled.
                $metadata->setLifecycleCallbacks([]);
            }
        });

        // Delete old fixtures data.
        $purger = new ORMPurger();
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_DELETE);

        /** @var SymfonyFixturesLoader $loader */
        $loader = $symfony->_getContainer()->get('test.doctrine.fixtures.loader');
        // Load specified fixture by class name.
        // The fixture is getting from Symfony DI, so it should be registered.
        $fixture = $loader->getFixture($className);

        $executor = new ORMExecutor($entityManager, $purger);
        // Load the fixture with its dependencies to the database.
        $executor->execute($loader->getFixtures(), false);

        $entityManager->close();

        return $fixture;
    }
}
