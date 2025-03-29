<?php

namespace App\ApiResource;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Entity\Category;
use App\Entity\Product;
use App\State\EntityClassDtoStateProcessor;
use App\State\EntityToDtoStateProvider;
use App\State\ProductStateProcessor;
use App\Validator\IsValidSupplier;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ApiResource(
    shortName: 'Product',
    operations: [
        new Get(),
        new GetCollection(),
        new Post(
            security: 'is_granted("ROLE_PRODUCT_CREATE")'
        ),
        new Patch(
            security: 'is_granted("EDIT",object)'
        ),
        new Delete(
            security: 'is_granted("ROLE_ADMIN")'
        )
    ],
    paginationItemsPerPage: 10,
    provider: EntityToDtoStateProvider::class,
    processor: ProductStateProcessor::class,
    stateOptions: new Options(entityClass: Product::class),
)]
class ProductApi
{
    #[ApiProperty(readable: false, writable: false, identifier: true)]
    public ?int $id = null;

    #[NotBlank]
    public ?string $name = null;

    #[NotBlank]
    public ?string $description = null;

    #[GreaterThanOrEqual(0)]
    public ?int $quantity = 0;

    #[GreaterThanOrEqual(0)]
    public ?float $price = 0;

    #[ApiProperty(security: 'object === null or is_granted("EDIT",object)')]
    public bool $isVerified = false;

    #[IsValidSupplier]
    public ?UserApi $supplier = null;


    /**
     * @var array <int,Category>
     */
    public array $category;

    public ?\DateTime $createdAt;

    public ?bool $isMine = false;


}