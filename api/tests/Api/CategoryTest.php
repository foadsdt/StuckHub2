<?php

namespace App\Tests\Api;

use App\Factory\CategoryFactory;
use App\Factory\UserFactory;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class CategoryTest extends ApiTestCase
{
    /**
     * @throws TransportExceptionInterface
     */
    public function testGetCollectionOfCategories(): void
    {

        $user = UserFactory::createOne(['password' => '0000', 'roles' => ['ROLE_USER']]);
        $userToken = $this->getUserToken($user);

        CategoryFactory::createMany(5);

        $json = $this->browser()
            ->get('/categories',[
                'headers' => [
                    'Accept' => 'application/ld+json',
                    'Content-Type' => 'application/ld+json; charset=utf-8',
                    'Authorization' => 'Bearer ' . $userToken
                ]
            ])
            ->assertStatus(200)
            ->assertJson()
            ->assertJsonMatches('"totalItems"', 5)
            ->json();
//        $json->assertMatches('keys(member[0])', [
//            '@id',
//            '@type',
//            'id',
//            'name',
//        ]);

        $this->assertSame(array_keys($json->decoded()['member'][0]), [
            '@id',
            '@type',
            'id',
            'name',
        ]);

    }


}