<?php

declare(strict_types = 1);

namespace App\Service;

use App\Entity\EntityResourceInterface;
use App\Service\Exception\ResourceNotFoundException;
use App\Service\Exception\ServiceException;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;

/**
 * Interface for CRUD services implementation.
 *
 * @author Anton Pelykh <anton.pelykh.dev@gmail.com>
 */
interface CrudServiceInterface
{
    /**
     * @param int $id
     *
     * @return null|EntityResourceInterface
     * @throws ServiceException
     */
    public function get(int $id): ?EntityResourceInterface;

    /**
     * @param Criteria $criteria
     *
     * @return Collection
     * @throws ServiceException
     */
    public function getList(Criteria $criteria): Collection;

    /**
     * @param EntityResourceInterface $entity
     *
     * @throws ServiceException
     */
    public function create(EntityResourceInterface $entity): void;

    /**
     * @param EntityResourceInterface $entity
     *
     * @throws ServiceException
     */
    public function update(EntityResourceInterface $entity): void;

    /**
     * @param int $id
     *
     * @throws ServiceException
     * @throws ResourceNotFoundException
     */
    public function delete(int $id): void;
}
