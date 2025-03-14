<?php

namespace App\Validator;

use App\Entity\Product;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class ProductsAllowedSupplierChangeValidator extends ConstraintValidator
{

    public function __construct(private EntityManagerInterface $entityManager,
                                private Security               $security)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {

        assert($constraint instanceof ProductsAllowedSupplierChange);

        /* @var ProductsAllowedSupplierChange $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        assert($value instanceof Collection);

        $unitOfWork = $this->entityManager->getUnitOfWork();

        foreach ($value as $product) {
            assert($product instanceof Product);

            $originalProduct = $unitOfWork->getOriginalEntityData($product);
            $originalSupplierId = $originalProduct['supplier_id'];
            $newSupplierId = $product->getSupplier()->getId();

            if (!$originalSupplierId || $originalSupplierId === $newSupplierId || $this->security->isGranted('ROLE_ADMIN')) {
                return;
            }

            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }


    }
}
