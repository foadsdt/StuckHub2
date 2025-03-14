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

        UserFactory::createMany(10,function (){
            return [
                'password'=>'0000'
            ];
        });
        CategoryFactory::createMany(20);

        ProductFactory::createMany(40, function () {
            return [
                'supplier' => UserFactory::random(),
                'category' => CategoryFactory::random(),
            ];
        });
    }
}
