<?php

namespace App\ApiResource;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use App\Entity\User;
use App\State\EntityClassDtoStateProcessor;
use App\State\EntityToDtoStateProvider;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

#[ApiResource(
    shortName: 'User',
//    normalizationContext: [AbstractNormalizer::IGNORED_ATTRIBUTES => ['newCustomIntField']],
//    denormalizationContext: [AbstractNormalizer::IGNORED_ATTRIBUTES => ['newCustomIntField']],
    paginationItemsPerPage: 5,
    provider: EntityToDtoStateProvider::class,
    processor: EntityClassDtoStateProcessor::class,
    stateOptions: new Options(entityClass: User::class)
)]
#[ApiFilter(SearchFilter::class, properties: ['username' => 'partial'])]
class UserApi
{
    #[ApiProperty(readable: false, writable: false, identifier: true)]
    public ?int $id = null;

    public ?string $email = null;

    public ?string $username = null;

    /**
     * The plainText when being set or changed.
     */
    #[ApiProperty(readable: false)]
    public ?string $password = null;

    /**
     * @var array <int, Product>
     */
    #[ApiProperty(writable: false)]
    public array $products = [];

//    #[ApiProperty(readable: false)]
//    #[Ignore]
    #[ApiProperty(writable: false)]
    public int $newCustomIntField = 0;

}