<?php

namespace App\Story;

use App\Factory\CategoryFactory;
use Zenstruck\Foundry\Story;

final class DefaultCategoriesStory extends Story
{
    public function build(): void
    {
        // TODO build your story here (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#stories)
        CategoryFactory::createMany(5);
    }
}
