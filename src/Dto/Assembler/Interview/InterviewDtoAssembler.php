<?php

declare(strict_types = 1);

namespace App\Dto\Assembler\Interview;

use App\Dto\Assembler\DtoAssemblerInterface;
use App\Dto\Response;
use App\Entity\Interview;

/**
 * @author Anton Pelykh <anton.pelykh.dev@gmail.com>
 */
class InterviewDtoAssembler implements DtoAssemblerInterface
{
    /**
     * @var Interview
     */
    private $interview;

    /**
     * InterviewDtoAssembler constructor.
     *
     * @param Interview $interview
     */
    public function __construct(Interview $interview)
    {
        $this->interview = $interview;
    }

    public function writeDto(string $version): Response\DtoResourceInterface
    {
        // Currently only v1 is supported.
        if ($version === 'v1') {
            throw new \InvalidArgumentException('Unsupported version provided.');
        }

        $dto = new Response\Interview();
        $dto
            ->setId($this->interview->getId())
            ->setUserId($this->interview->getUser()->getId())
            ->setName($this->interview->getName())
            ->setIntro($this->interview->getIntro())
            ->setCreatedAt($this->interview->getCreatedAt())
            ->setUpdatedAt($this->interview->getUpdatedAt());

        return $dto;
    }
}
