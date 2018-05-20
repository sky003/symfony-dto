<?php

declare(strict_types = 1);

namespace Helper;

use Codeception\Module;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

/**
 * Fixtures helper.
 *
 * @author Anton Pelykh <anton.pelykh.dev@gmail.com>
 */
class Fixtures extends Module
{
    /**
     * @param \Doctrine\Common\DataFixtures\FixtureInterface $fixture
     *
     * @return \App\Component\Doctrine\Common\DataFixtures\AbstractFixture
     * @throws \Codeception\Exception\ModuleException
     */
    public function loadFixture(FixtureInterface $fixture): FixtureInterface
    {
        /** @var Module\Symfony $symfony */
        $symfony = $this->getModule('Symfony');

        if ($fixture instanceof ContainerAwareInterface) {
            $fixture->setContainer($symfony->_getContainer());
        }

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

        $purger = new ORMPurger();
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_DELETE);
        $loader = new Loader();
        $loader->addFixture($fixture);
        $executor = new ORMExecutor($entityManager, $purger);
        $executor->execute($loader->getFixtures(), false);

        $entityManager->close();

        return $fixture;
    }
}
