<?php

namespace App\ApiResource;

use App\Enum\DailyQuestStatusEnum;

class QuestProduct
{
    public function __construct(
        public string $name,
        public int $quantity,
        public $price,
    )
    {
    }
}