<?php

namespace App\Security\Voter;

use App\Entity\ShoppingList;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ShoppingListVoter extends Voter
{
    public const VIEW = 'LIST_VIEW';
    public const EDIT = 'LIST_EDIT';
    public const DELETE = 'LIST_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::EDIT, self::DELETE])
            && $subject instanceof ShoppingList;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?\Symfony\Component\Security\Core\Authorization\Voter\Vote $vote = null): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        /** @var ShoppingList $list */
        $list = $subject;

        return match($attribute) {
            self::VIEW, self::EDIT, self::DELETE => $this->canAccess($list, $user),
            default => false,
        };
    }

    private function canAccess(ShoppingList $list, UserInterface $user): bool
    {
        // Der Besitzer hat vollen Zugriff
        return $list->getOwner() === $user;
    }
}
