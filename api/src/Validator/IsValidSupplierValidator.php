<?php

namespace App\Validator;

use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class IsValidSupplierValidator extends ConstraintValidator
{

    public function __construct(private Security $security)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {

        assert($constraint instanceof IsValidSupplier);

        /* @var IsValidSupplier $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        assert($value instanceof User);

        $user = $this->security->getUser();
        if (!$user) {
            throw new \LogicException('IsSupplierValidator should only be used when a user is logged in');
        }

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return;
        }

        if ($value !== $user) {
            $this->context->buildViolation($constraint->message)
                //->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
