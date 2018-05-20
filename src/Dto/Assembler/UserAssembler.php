<?php

declare(strict_types = 1);

namespace App\Dto\Assembler;

use App\Entity\User;
use App\Dto\Response;

class UserAssembler
{
    public function writeDto(User $user): Response\User
    {
        $dto = new Response\User();
        $dto
            ->setId($user->getId())
            ->setEmail($user->getEmail())
            ->setRole(
                $this->buildStringRepresentationOfRole($user->getRole()))
            ->setStatus(
                $this->buildStringRepresentationOfStatus($user->getStatus()))
            ->setCreatedAt($user->getCreatedAt())
            ->setUpdatedAt($user->getUpdatedAt());

        return $dto;
    }

    protected function buildStringRepresentationOfRole(int $role): string
    {
        switch($role) {
            case User::ROLE_INTERVIEWEE:
                return 'INTERVIEWEE';
            case User::ROLE_INTERVIEWER:
                return 'INTERVIEWER';
            case User::ROLE_ADMIN:
                return 'ADMIN';
            default:
                throw new \LogicException(
                    sprintf('Can not find a string representation of role "%d".', $role)
                );
        }
    }

    protected function buildStringRepresentationOfStatus(int $status): string
    {
        switch($status) {
            case User::STATUS_ENABLED:
                return 'ENABLED';
            case User::STATUS_UNVERIFIED:
                return 'UNVERIFIED';
            case User::STATUS_LOCKED:
                return 'LOCKED';
            default:
                throw new \LogicException(
                    sprintf('Can not find a string representation of status "%d".', $status)
                );
        }
    }
}
