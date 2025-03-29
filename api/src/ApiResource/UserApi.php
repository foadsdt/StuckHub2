<?php

namespace App\ApiResource;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Entity\User;
use App\State\EntityClassDtoStateProcessor;
use App\State\EntityToDtoStateProvider;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ApiResource(
    shortName: 'User',
//    normalizationContext: [AbstractNormalizer::IGNORED_ATTRIBUTES => ['newCustomIntField']],
//    denormalizationContext: [AbstractNormalizer::IGNORED_ATTRIBUTES => ['newCustomIntField']],
    operations: [
        new Get(),
        new GetCollection(),
        new Post(
            security: 'is_granted("PUBLIC_ACCESS")',
            validationContext: ['groups' => ['Default', 'postValidation']],
        ),
        new Patch(
            security: 'is_granted("ROLE_USER_EDIT")',
        ),
        new Delete(),
    ],
    paginationItemsPerPage: 5,
    security: 'is_granted("ROLE_USER")',
    provider: EntityToDtoStateProvider::class,
    processor: EntityClassDtoStateProcessor::class,
    stateOptions: new Options(entityClass: User::class),
)]
#[ApiFilter(SearchFilter::class, properties: ['username' => 'partial'])]
class UserApi
{
    #[ApiProperty(readable: false, writable: false, identifier: true)]
    public ?int $id = null;

    #[NotBlank]
    #[Email]
    public ?string $email = null;

    #[NotBlank]
    public ?string $username = null;

    /**
     * The plainText when being set or changed.
     */
    #[ApiProperty(readable: false)]
    #[NotBlank(groups: ['postValidation'])]
    public ?string $password = null;

    /**
     * @var array <int, ProductApi>
     */
    #[ApiProperty()]
    public array $products = [];

//    #[ApiProperty(readable: false)]
//    #[Ignore]
    #[ApiProperty(writable: false)]
    public int $newCustomIntField = 0;

}