<?php

namespace App\Story;

use App\Factory\NotificationFactory;
use Zenstruck\Foundry\Story;

final class DefaultNotificationsStory extends Story
{
    public function build(): void
    {
        // TODO build your story here (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#stories)
        NotificationFactory::createMany(10);
    }
}
