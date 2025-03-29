<?php

namespace App\Security\Voter;

use App\ApiResource\ProductApi;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class ProductApiVoter extends Voter
{
    public const EDIT = 'EDIT';

    // public const VIEW = 'POST_VIEW';

    public function __construct(private Security $security)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {

        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT])
            && $subject instanceof ProductApi;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof User) {
            return false;
        }

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        assert($subject instanceof ProductApi);

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::EDIT:

                if (!$this->security->isGranted('ROLE_PRODUCT_EDIT')) {
                    return false;
                }

                if ($subject->supplier?->id === $user->getId()) {
                    return true;
                }

                break;
        }

        return false;
    }
}
