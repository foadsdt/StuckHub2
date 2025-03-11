<?php

namespace App\Story;

use App\Entity\Product;
use App\Factory\CategoryFactory;
use App\Factory\ProductFactory;
use App\Factory\UserFactory;
use Zenstruck\Foundry\Story;

final class DefaultProductsStory extends Story
{
    public function build(): void
    {

        UserFactory::createMany(10);
        CategoryFactory::createMany(10);

        ProductFactory::createMany(10, function () {
            return [
                'supplier' => UserFactory::random(),
                'category' => CategoryFactory::random(),
            ];
        });
    }
}
