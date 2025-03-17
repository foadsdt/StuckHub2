<?php

namespace App\State;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\DailyQuest;
use App\ApiResource\QuestProduct;
use App\Enum\DailyQuestStatusEnum;
use App\Repository\ProductRepository;
use DateMalformedStringException;

class DailyQuestStateProvider implements ProviderInterface
{
    public function __construct(
        private ProductRepository $productRepository,
    )
    {
    }

    /**
     * @throws DateMalformedStringException
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        /*return [
//            new DailyQuest(4),
//            new DailyQuest(5)

        new DailyQuest(new \DateTime('now')),
        new DailyQuest(new \DateTime('yesterday')),
        ];*/

        if ($operation instanceof CollectionOperationInterface) {
            return $this->createQuests();
        }

        $quests = $this->createQuests();
        return $quests[$uriVariables['stringDate']] ?? null;


    }

    /**
     * @throws DateMalformedStringException
     */
    private function createQuests(): array
    {

        $products = $this->productRepository->findBy([], [], 10);

        $quests = [];

        for ($i = 0; $i < 50; $i++) {
            $quest = new DailyQuest(new \DateTimeImmutable(sprintf('- %d days', $i)));
            $quest->questName = sprintf('Quest %d', $i);
            $quest->description = sprintf('Description %d', $i);
            $quest->difficultyLevel = $i % 10;
            $quest->status = $i % 2 === 0 ? DailyQuestStatusEnum::ACTIVE : DailyQuestStatusEnum::COMPLETED;
            $quest->lastUpdatedAt = new \DateTimeImmutable(sprintf('- %d days', rand(10, 100)));

            $randomProductsKeys = array_rand($products, rand(1, 3));
            $randomProducts = array_map(fn($key) => $products[$key], (array)$randomProductsKeys);
//            $quest->products = $randomProducts;
            $questProducts = [];
            foreach ($randomProducts as $product) {
                $questProducts[] = new QuestProduct(
                    $product->getName(),
                    $product->getQuantity(),
                    $product->getPrice(),
                );
            }
            $quest->products = $questProducts;


            $quests[$quest->getStringDate()] = $quest;
        }

        return $quests;
    }


}
