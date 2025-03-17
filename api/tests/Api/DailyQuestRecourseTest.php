<?php

namespace App\Tests\Api;

class DailyQuestRecourseTest extends ApiTestCase
{
    public function testPatchCanUpdateStatus()
    {
//        $yesterday = new \DateTime('-1 day');
        $day = new \DateTime('-2 day');

        $this->browser()
            ->patch('/quests/' . $day->format('Y-m-d'), [
                'json' => [
                    'status' => 'completed',
                ],
                'headers' => [
                    'Accept' => 'application/ld+json',
                    'Content-Type' => 'application/merge-patch+json; charset=utf-8',
                ]
            ])
            ->assertStatus(200)
            ->assertJsonMatches('status', 'completed')
            ->dump();

    }
}