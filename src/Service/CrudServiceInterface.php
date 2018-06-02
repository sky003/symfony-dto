<?php

declare(strict_types = 1);

namespace App\Service;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;

/**
 * Interface for CRUD services implementation.
 *
 * @author Anton Pelykh <anton.pelykh.dev@gmail.com>
 */
interface CrudServiceInterface
{
    public function get(int $id): ?object;
    public function getList(Criteria $criteria): Collection;
    public function create(object $entity): void;
    public function update(object $entity): void;
    public function delete(int $id): void;
}
