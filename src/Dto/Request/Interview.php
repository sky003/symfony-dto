<?php

declare(strict_types = 1);

namespace App\Dto\Request;

use App\Component\Validator\Constraints as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Anton Pelykh <anton.pelykh.dev@gmail.com>
 */
class Interview extends AbstractPropertyChangeTracker implements DtoResourceInterface
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
     * @AppAssert\ExistEntity(
     *     entityClass="App\Entity\Interview",
     *     properties={"id"},
     *     groups={"OpUpdate"},
     * )
     */
    private $id;
    /**
     * @var string
     *
     * @Assert\NotBlank(
     *     groups={"OpCreate", "OpUpdate"},
     * )
     * @Assert\Length(
     *     max=255,
     *     groups={"OpCreate", "OpUpdate"},
     * )
     */
    private $name;
    /**
     * @var string
     *
     * @Assert\Length(
     *     max=1000,
     *     groups={"OpCreate", "OpUpdate"},
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
     */
    public function setId(int $id): void
    {
        $this->registerPropertyChanged('id');

        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return self
     */
    public function setName(?string $name): Interview
    {
        $this->registerPropertyChanged('name');

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
        $this->registerPropertyChanged('intro');

        $this->intro = $intro;

        return $this;
    }
}
