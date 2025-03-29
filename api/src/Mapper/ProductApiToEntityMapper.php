<?php

namespace App\Mapper;

use App\ApiResource\ProductApi;
use App\Entity\Category;
use App\Entity\Product;
use App\Entity\User;
use App\Repository\ProductRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;

#[AsMapper(from: ProductApi::class, to: Product::class)]
class ProductApiToEntityMapper implements MapperInterface
{

    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly Security          $security,
        private MicroMapperInterface       $microMapper,
    )
    {
    }

    public function load(object $from, string $toClass, array $context): object
    {
        $dto = $from;
        assert($dto instanceof ProductApi);

        $entity = $dto->id ? $this->productRepository->find($dto->id) : new Product($dto->name);
        if (!$entity) {
            throw new \Exception('Product not found');
        }

        return $entity;

    }

    public function populate(object $from, object $to, array $context): object
    {
        $dto = $from;
        assert($dto instanceof ProductApi);

        $entity = $to;
        assert($entity instanceof Product);

        if ($dto->supplier) {
            // UserApi --> User
            $entity->setSupplier($this->microMapper->map($dto->supplier, User::class, [
                MicroMapperInterface::MAX_DEPTH => 0
            ]));
        } else {
            $entity->setSupplier($this->security->getUser());
        }

        $entity->setDescription($dto->description);
        $entity->setPrice($dto->price);
        $entity->setQuantity($dto->quantity);

        $entity->setIsVerified($dto->isVerified);

        return $entity;
    }

}