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

    public function testProductsCanBeRemoved()
    {
        $user = UserFactory::createOne([
                'roles' => ['ROLE_USER_EDIT']
            ]
        );

        $otherUser = UserFactory::createOne();

        $product = ProductFactory::createOne(['supplier' => $user, 'isVerified' => true]);

        ProductFactory::createOne(['supplier' => $user, 'isVerified' => true]);

        $product3 = ProductFactory::createOne(['supplier' => $otherUser, 'isVerified' => true]);

        $res =
            $this->browser()
                ->actingAs($user)
                ->patch('/users/' . $user->getId(), [
                    'json' => [
                        'products' => [
                            '/products/' . $product->getId(),
                            '/products/' . $product3->getId()
                        ],
                    ],
                    'headers' => [
                        'Accept' => 'application/ld+json',
                        'Content-Type' => 'application/merge-patch+json; charset=utf-8',
                    ]
                ])
                ->assertStatus(200)
//                ->get('/users/' . $user->getId())
//            ->dump()
//            ->json()
//            ->decoded()
//            ->assertJsonMatches('length("products")', 2)
//            ->assertJsonMatches('products[0]', '/products/' . $product->getId())
//            ->assertJsonMatches('products[1]', '/products/' . $product3->getId())
        ;
//        dd($res->get('/users/' . $user->getId())->json()->decoded());
//        die;
//        dd($res->json()->decoded()['products']);

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