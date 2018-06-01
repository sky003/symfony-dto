<?php

declare(strict_types = 1);

namespace App\Dto\Response;

/**
 * @author Anton Pelykh <anton.pelykh.dev@gmail.com>
 */
class Interview implements DtoResourceInterface
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var int
     */
    private $userId;
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $intro;
    /**
     * @var \DateTime
     */
    private $createdAt;
    /**
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return self
     */
    public function setId(int $id): Interview
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     *
     * @return self
     */
    public function setUserId(int $userId): Interview
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return self
     */
    public function setName(string $name): Interview
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getIntro(): ?string
    {
        return $this->intro;
    }

    /**
     * @param string $intro
     *
     * @return self
     */
    public function setIntro(?string $intro): Interview
    {
        $this->intro = $intro;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     *
     * @return self
     */
    public function setCreatedAt(\DateTime $createdAt): Interview
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     *
     * @return self
     */
    public function setUpdatedAt(?\DateTime $updatedAt): Interview
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
