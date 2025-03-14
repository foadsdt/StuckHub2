<?php

namespace App\Tests\Api;

use App\Factory\ProductFactory;
use App\Factory\UserFactory;

class UserTest extends ApiTestCase
{
    public function testPostToCreateUser()
    {

        $res = $this->browser()
            ->post('/users', [
                'json' => [
                    'email' => 'test@test.com',
                    'password' => 'password',
                    'username' => 'test',
                    'firstName' => 'test',
                    'lastName' => 'test'
                ],
                'headers' => [
                    'Accept' => 'application/ld+json',
                    'Content-Type' => 'application/ld+json; charset=utf-8',
                ]
            ])
            ->assertStatus(201);
    }

    public function testPatchToUpdateUser()
    {

        $user = UserFactory::createOne([
                'roles' => ['ROLE_USER_EDIT']
            ]
        );

        $this->browser()
            ->actingAs($user)
            ->patch('/users/' . $user->getId(), [
                'json' => [
                    'username' => 'test2',
                ],
                'headers' => [
                    'Accept' => 'application/ld+json',
                    'Content-Type' => 'application/merge-patch+json; charset=utf-8',
                ]
            ])
            ->assertStatus(200);;
    }

    public function testProductsCannotBeStolen()
    {

        $user = UserFactory::createOne([
                'roles' => ['ROLE_USER_EDIT']
            ]
        );

        $otherUser = UserFactory::createOne();

        $product = ProductFactory::createOne([
            'supplier' => $otherUser
        ]);

        $this->browser()
            ->actingAs($user)
            ->patch('/users/' . $user->getId(), [
                'json' => [
                    'username' => 'test2',
                    'products' => ['/products/' . $product->getId()],
                ],
                'headers' => [
                    'Accept' => 'application/ld+json',
                    'Content-Type' => 'application/merge-patch+json; charset=utf-8',
                ]
            ])
            ->assertStatus(422);;
    }


}