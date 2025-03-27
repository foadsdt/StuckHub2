<?php

namespace App\Mapper;

use App\ApiResource\ProductApi;
use App\ApiResource\UserApi;
use App\Entity\Product;
use Symfony\Bundle\SecurityBundle\Security;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;

#[AsMapper(from: Product::class, to: ProductApi::class)]
class ProductEntityToApiMapper implements MapperInterface
{

    public function __construct(
        private readonly MicroMapperInterface $microMapper,
        private readonly Security             $security,
    )
    {
    }

    public function load(object $from, string $toClass, array $context): object
    {
        $entity = $from;
        assert($entity instanceof Product);

        $dto = new ProductApi();
        $dto->id = $entity->getId();

        return $dto;

    }

    public function populate(object $from, object $to, array $context): object
    {
        $entity = $from;
        assert($entity instanceof Product);

        $dto = $to;
        assert($dto instanceof ProductApi);

        $dto->name = $entity->getName();
        $dto->description = $entity->getDescription();
        $dto->quantity = $entity->getQuantity();
        $dto->price = $entity->getPrice();
        $dto->supplier = $this->microMapper->map($entity->getSupplier(), UserApi::class,[
            MicroMapperInterface::MAX_DEPTH => 0
        ]);
        $dto->category = [];
        $dto->createdAt = $entity->getCreatedAt();
        $dto->isMine = $this->security->getUser() && $this->security->getUser() === $entity->getSupplier();

        return $dto;
    }
}