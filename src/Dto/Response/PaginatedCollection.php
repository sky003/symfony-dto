<?php

declare(strict_types = 1);

namespace App\Dto\Response;

use Doctrine\Common\Collections\Collection;

/**
 * Class PaginatedCollection.
 *
 * @author Anton Pelykh <anton.pelykh.dev@gmail.com>
 */
class PaginatedCollection
{
    /**
     * @var Collection
     */
    private $embedded;
    /**
     * @var int
     */
    private $offset;
    /**
     * @var int
     */
    private $limit;
    /**
     * @var int
     */
    private $total;

    /**
     * PaginatedCollection constructor.
     *
     * @param Collection $embedded
     * @param int        $offset
     * @param int        $limit
     * @param int|null   $total    Sometimes you will need to provide a number of collection elements,
     *                             e.g. in the case you working with lazy collections.
     */
    public function __construct(Collection $embedded, int $offset = 0, int $limit = 10, ?int $total = null)
    {
        $this->embedded = $embedded;
        $this->offset   = $offset;
        $this->limit    = $limit;
        $this->total    = $total ?? \count($embedded);
    }

    /**
     * @return Collection
     */
    public function getEmbedded(): Collection
    {
        return $this->embedded;
    }

    /**
     * @param Collection $embedded
     *
     * @return self
     */
    public function setEmbedded(Collection $embedded): PaginatedCollection
    {
        $this->embedded = $embedded;

        return $this;
    }

    /**
     * @return int
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * @param int $offset
     *
     * @return self
     */
    public function setOffset(int $offset): PaginatedCollection
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     *
     * @return self
     */
    public function setLimit(int $limit): PaginatedCollection
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @return int
     */
    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * @param int $total
     *
     * @return self
     */
    public function setTotal(int $total): PaginatedCollection
    {
        $this->total = $total;

        return $this;
    }
}
