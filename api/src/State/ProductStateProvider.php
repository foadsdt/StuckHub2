<?php

namespace App\State;

use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use ApiPlatform\Doctrine\Orm\State\ItemProvider;
use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Product;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class ProductStateProvider implements ProviderInterface
{

    public function __construct(
        #[Autowire(service: ItemProvider::class)]
        private ProviderInterface $itemProvider,
        #[Autowire(service: CollectionProvider::class)]
        private ProviderInterface $collectionProvider,
        private Security          $security,
    )
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {

        if ($operation instanceof CollectionOperationInterface) {

            /** @var iterable<Product> $paginator */
            $paginator = $this->collectionProvider->provide($operation, $uriVariables, $context);

            foreach ($paginator as $product) {
                $product->setIsSuppliedByAuthenticatedUser(
                    $this->security->getUser() === $product->getSupplier()
                );
            }

            return $paginator;
        }

        $product = $this->itemProvider->provide($operation, $uriVariables, $context);

        if (!$product instanceof Product) {
            return $product;
        }

        $product->setIsSuppliedByAuthenticatedUser(
            $this->security->getUser() === $product->getSupplier()
        );

        return $product;
    }
}
