<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use App\Enum\DailyQuestStatusEnum;
use App\State\DailyQuestStateProvider;
use Symfony\Component\Serializer\Annotation\Ignore;

#[ApiResource(
    shortName: 'Quest',
    provider: DailyQuestStateProvider::class
)]
class DailyQuest
{

//    public int $id;

    #[Ignore]
    public \DateTimeImmutable $date;


    public string $questName;
    public string $description;
    public int $difficultyLevel;
    public DailyQuestStatusEnum  $status;



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