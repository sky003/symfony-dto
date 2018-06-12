<?php

declare(strict_types = 1);

namespace App\Component\Security\Core\Authorization\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * AbstractCrudVoter that mainly can be used in AbstractCrudController.
 *
 * Make sure you create an abstract voter implementation for each
 * abstract controller implementation.
 *
 * @see \App\Controller\AbstractCrudController
 *
 * @author Anton Pelykh <anton.pelykh.dev@gmail.com>
 */
abstract class AbstractCrudVoter extends Voter
{
    public const VIEW = 'view';
    public const EDIT = 'edit';
    public const DELETE = 'delete';

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject): bool
    {
        if (!\in_array($attribute, [self::VIEW, self::EDIT, self::DELETE], true)) {
            return false;
        }

        return $this->supportsSubject($subject);
    }

    /**
     * @param object $subject An entity object.
     *
     * @return bool
     */
    abstract protected function supportsSubject(object $subject): bool;
}
