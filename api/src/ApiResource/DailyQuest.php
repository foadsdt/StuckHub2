<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use App\Entity\Product;
use App\Enum\DailyQuestStatusEnum;
use App\State\DailyQuestStateProcessor;
use App\State\DailyQuestStateProvider;
use Symfony\Component\Serializer\Annotation\Ignore;

#[ApiResource(
    shortName: 'Quest',
    operations: [
        new GetCollection(),
        new Get(),
        new Patch()
    ],
    paginationItemsPerPage: 10,
    provider: DailyQuestStateProvider::class,
    processor: DailyQuestStateProcessor::class,
)]
class DailyQuest
{

//    public int $id;

    #[Ignore]
    public \DateTimeImmutable $date;


    public string $questName;
    public string $description;
    public int $difficultyLevel;
    public DailyQuestStatusEnum $status;

    public \DateTimeInterface $lastUpdatedAt;

    /**
     * @var Product[]
     */
    #[ApiProperty(genId: false)]
    public array $products;

    public function __construct(/*int $id*/ \DateTimeImmutable $date)
    {
        $this->date = $date;
//        $this->id = $id;
    }

    #[ApiProperty(identifier: true)]
    public function getStringDate(): string
    {
        return $this->date->format('Y-m-d');
    }

}