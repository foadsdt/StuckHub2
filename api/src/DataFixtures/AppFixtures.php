<?php

namespace App\DataFixtures;

use App\Story\App\Entity\UserStory;
use App\Story\DefaultCategoriesStory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

//        $manager->flush();
        UserStory::load();

//        DefaultCategoriesStory::load();

    }
}
