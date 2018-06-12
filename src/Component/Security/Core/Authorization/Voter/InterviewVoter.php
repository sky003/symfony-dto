<?php

declare(strict_types = 1);

namespace App\Component\Security\Core\Authorization\Voter;

use App\Component\Security\Core\User\UserInterface;
use App\Entity\Interview;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

/**
 * Class InterviewVoter.
 *
 * @author Anton Pelykh <anton.pelykh.dev@gmail.com>
 */
class InterviewVoter extends AbstractCrudVoter
{
    /**
     * @var AccessDecisionManagerInterface
     */
    private $accessDecisionManager;

    public function __construct(AccessDecisionManagerInterface $accessDecisionManager)
    {
        $this->accessDecisionManager = $accessDecisionManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function supportsSubject(object $subject): bool
    {
        return $subject instanceof Interview;
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        // Admin can do anything.
        if ($this->accessDecisionManager->decide($token, ['ROLE_ADMIN'])) {
            return true;
        }

        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            // The user must be logged in.
            return false;
        }

        /**
         * @var Interview $interview
         */
        $interview = $subject;

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($interview, $user);
            case self::EDIT:
                return $this->canEdit($interview, $user);
            case self::DELETE:
                return $this->canDelete($interview, $user);
            default:
                throw new \LogicException(
                    sprintf('Unknown attribute "%s" provided.', $attribute)
                );
        }
    }

    private function canView(Interview $interview, UserInterface $user): bool
    {
        // If user can edit, they can view.
        if ($this->canEdit($interview, $user)) {
            return true;
        }

        return false;
    }

    private function canDelete(Interview $interview, UserInterface $user): bool
    {
        // If user can edit, they can delete.
        if ($this->canEdit($interview, $user)) {
            return true;
        }

        return false;
    }

    private function canEdit(Interview $interview, UserInterface $user): bool
    {
        $isOwner = $user->getIdentifier() === $interview->getUser()->getId();

        if ($isOwner) {
            return true;
        }

        return false;
    }
}
