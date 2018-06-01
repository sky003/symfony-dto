<?php

declare(strict_types = 1);

namespace App\Dto\Request;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Anton Pelykh <anton.pelykh.dev@gmail.com>
 */
class Interview implements DtoResourceInterface
{
    /**
     * @var int
     *
     * @Assert\IsNull(
     *     groups={"OpCreate"},
     * )
     * @Assert\NotNull(
     *     groups={"OpUpdate"},
     * )
     */
    private $id;
    /**
     * @var int
     *
     * @Assert\NotNull(
     *     groups={"OpCreate"},
     * )
     * @Assert\IsNull(
     *     groups={"OpUpdate"},
     * )
     */
    private $userId;
    /**
     * @var string
     *
     * @Assert\NotBlank(
     *     groups={"OpCreate"}
     * )
     * @Assert\Length(
     *     max=255,
     * )
     */
    private $name;
    /**
     * @var string
     *
     * @Assert\Length(
     *     max=1000,
     * )
     */
    private $intro;

    /**
     * {@inheritdoc}
     */
    public function getId(): ?int
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
    public function getIntro(): string
    {
        return $this->intro;
    }

    /**
     * @param string $intro
     *
     * @return self
     */
    public function setIntro(string $intro): Interview
    {
        $this->intro = $intro;

        return $this;
    }
}
