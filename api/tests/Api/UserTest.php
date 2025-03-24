<?php

namespace App\Tests\Api;

use App\Factory\ProductFactory;
use App\Factory\UserFactory;
use Zenstruck\Browser\Json;

class UserTest extends ApiTestCase
{
    public function testPostToCreateUser()
    {

//        $res = $this->browser()
        $this->browser()
            ->post('/users', [
                'json' => [
                    'email' => 'test@test.com',
                    'password' => 'password',
                    'username' => 'test',
                ],
                'headers' => [
                    'Accept' => 'application/ld+json',
                    'Content-Type' => 'application/ld+json; charset=utf-8',
                ]
            ])
            ->use(function (Json $json) {
                $json
                    ->assertMissing('id')
                    ->assertMissing('password');

            })
            ->assertStatus(201);
//        dd($res->json());
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
//                    'newCustomIntField' => 999
                    'id' => 47
                ],
                'headers' => [
                    'Accept' => 'application/ld+json',
                    'Content-Type' => 'application/merge-patch+json; charset=utf-8',
                ]
            ])
            ->assertStatus(200);

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

    public function testUnverifiedProductsNotReturned()
    {
        $user = UserFactory::createOne();
        $product = ProductFactory::createOne([
            'isVerified' => false,
            'supplier' => $user
        ]);

        $this->browser()
            ->actingAs(UserFactory::createOne())
            ->get('/users/' . $user->getId())
            ->assertJsonMatches('length("products")', 0);
    }


}