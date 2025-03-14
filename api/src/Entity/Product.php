<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Serializer\Filter\PropertyFilter;
use App\Repository\ProductRepository;
use App\Validator\IsValidSupplier;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(
            normalizationContext: [
                'groups' => ['product:read']
            ]
        ),
        new Post(
            security: 'is_granted("ROLE_PRODUCT_CREATE")',
        ),
        new Patch(
        // security: 'is_granted("ROLE_ADMIN") or (is_granted("ROLE_PRODUCT_EDIT") and object.getSupplier() == user)',
        // securityPostDenormalize: 'is_granted("ROLE_ADMIN") or (object.getSupplier() == user)',
            security: 'is_granted("EDIT",object)',
//            securityPostDenormalize: 'is_granted("EDIT",object)',
        ),
        new Delete(
            security: 'is_granted("ROLE_ADMIN")',
        )
    ],
    normalizationContext: ['groups' => ['product:read', 'product:item:get']],
    denormalizationContext: ['groups' => ['product:write']],
    paginationItemsPerPage: 30
)]
#[ApiResource(
    uriTemplate: '/users/{user_id}/products.{_format}',
    operations: [new GetCollection()],
    uriVariables: [
        'user_id' => new Link(
            fromProperty: 'products',
            fromClass: User::class
        )
    ],
    normalizationContext: ['groups' => ['product:read']]
)]
#[ApiFilter(PropertyFilter::class)]
#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ORM\Table]
#[ApiFilter(SearchFilter::class, strategy: 'partial', properties: ['supplier.username'])]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['product:read', 'product:write', 'user:read', 'user:write'])]
    #[ApiFilter(SearchFilter::class, strategy: 'partial')]
    #[Assert\NotBlank]
    private string $name;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['product:read', 'product:write', 'user:read', 'user:write'])]
//    #[ApiProperty(security: 'is_granted("EDIT",object)')]
    #[Assert\NotBlank]
    private ?string $description = null;

    #[ORM\Column]
    #[Groups(['product:read', 'product:write', 'user:read', 'user:write'])]
    #[ApiFilter(RangeFilter::class)]
    #[Assert\GreaterThanOrEqual(0)]
    private ?int $quantity = 0;

    #[ORM\Column]
    #[Groups(['product:read', 'product:write', 'user:read', 'user:write'])]
    #[Assert\GreaterThanOrEqual(0)]
    #[ApiFilter(RangeFilter::class)]
    private ?float $price = 0;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['product:read', 'product:write'])]
    #[Assert\Valid]
    #[Assert\NotNull]
    #[IsValidSupplier]
    #[ApiFilter(SearchFilter::class)]
    private ?User $supplier = null;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['product:read', 'product:write'])]
    #[Assert\NotNull]
    private ?Category $category = null;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['product:read'])]
    private ?\DateTime $createdAt;

    #[ORM\Column]
    #[ApiFilter(BooleanFilter::class)]
//    #[Groups(['product:read', 'product:write'])]
    #[ApiProperty(security: 'is_granted("EDIT",object)')]
    #[Groups(['admin:read', 'admin:write', 'supplier:read'])]
    private bool $isVerified = false;


    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;
        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): void
    {
        $this->quantity = $quantity;
    }

    /**
     * @param \DateTime|null $createdAt
     */
    public function setCreatedAt(?\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function getSupplier(): ?User
    {
        return $this->supplier;
    }

    public function setSupplier(?User $supplier): void
    {
        $this->supplier = $supplier;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): void
    {
        $this->category = $category;
    }

    public function getIsVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): void
    {
        $this->isVerified = $isVerified;
    }


}