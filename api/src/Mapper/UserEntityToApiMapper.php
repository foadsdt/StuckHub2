<?php

namespace App\Mapper;

use App\ApiResource\ProductApi;
use App\ApiResource\UserApi;
use App\Entity\Product;
use App\Entity\User;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;

#[AsMapper(from: User::class, to: UserApi::class)]
class UserEntityToApiMapper implements MapperInterface
{

    public function __construct(
        private MicroMapperInterface $microMapper
    )
    {
    }

    public function load(object $from, string $toClass, array $context): object
    {
        $entity = $from;
        assert($entity instanceof User);

        $dto = new UserApi();
        $dto->id = $entity->getId();

        return $dto;
    }

    public function populate(object $from, object $to, array $context): object
    {
        $entity = $from;
        assert($entity instanceof User);

        $dto = $to;
        assert($dto instanceof UserApi);

        $dto->id = $entity->getId();
        $dto->email = $entity->getEmail();
        $dto->username = $entity->getUsername();

        /** Product -> ProductApi **/

        $dto->products = array_map(function (Product $product) {
            return $this->microMapper->map($product, ProductApi::class, [
                MicroMapperInterface::MAX_DEPTH => 0
            ]);
        }, $entity->getVerifiedProducts()->getValues());

        //  $dto->products = array_map(fn($product) => $this->mapProductToDto($product), $entity->getProducts()->toArray());
        //  $dto->products = $entity->getProducts()->toArray();

        $dto->newCustomIntField = rand(1, 10);

        return $dto;
    }

    private function mapProductToDto(Product $product): array
    {
        return [
            'id' => $product->getId(),
            'name' => $product->getName(),
        ];
    }
}