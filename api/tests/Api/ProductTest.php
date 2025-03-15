<?php

namespace App\Tests\Api;

use App\Factory\CategoryFactory;
use App\Factory\ProductFactory;
use App\Factory\UserFactory;
use Zenstruck\Browser\HttpOptions;

class ProductTest extends ApiTestCase
{

    public function testGetCollectionOfProducts(): void
    {

        $user = UserFactory::createOne(['password' => '0000', 'roles' => ['ROLE_USER']]);

        ProductFactory::createMany(5);

        $json = $this->browser()
            ->actingAs($user)
            ->get('/products', [
                'headers' => [
                    'Accept' => 'application/ld+json',
                    'Content-Type' => 'application/ld+json; charset=utf-8',
                ]
            ])
            ->assertStatus(200)
            ->assertJson()
//            ->assertJsonMatches('"totalItems"', 5)
            ->json();

        $this->assertSame(array_keys($json->decoded()['member'][0]), [
            '@id',
            '@type',
            'name',
            'description',
            'quantity',
            'price',
            'supplier',
            'category',
            'createdAt',
            'isMine'
        ]);

    }

    public function testGetCollectionOfVerifiedProducts(): void
    {

        $user = UserFactory::createOne(['password' => '0000', 'roles' => ['ROLE_USER']]);

        ProductFactory::createMany(5, [
            'isVerified' => true,
        ]);

        ProductFactory::createOne([
            'isVerified' => false,
        ]);

        $json =
            $this->browser()
                ->actingAs($user)
                ->get('/products', [
                    'headers' => [
                        'Accept' => 'application/ld+json',
                        'Content-Type' => 'application/ld+json; charset=utf-8',
                    ]
                ])
                ->assertJsonMatches('"totalItems"', 5)
                ->json();
        ;

        $this->assertSame(array_keys($json->decoded()['member'][0]), [
            '@id',
            '@type',
            'name',
            'description',
            'quantity',
            'price',
            'supplier',
            'category',
            'createdAt',
            'isMine'
        ]);

    }

    public function testPostNewProduct()
    {
        $user = UserFactory::createOne(['password' => '0000', 'roles' => ['ROLE_PRODUCT_CREATE']]);
        $userToken = $this->getUserToken($user);

        $result = $this->browser()
            ->post('/products', [
                'json' => [
                    'name' => 'test',
                    'quantity' => 5,
                    'price' => 2000.0,
                    'description' => 'test description',
                    'category' => '/categories/' . CategoryFactory::createOne()->getId(),
                    'supplier' => '/users/' . $user->getId()
                ],
                'headers' => [
                    'Accept' => 'application/ld+json',
                    'Content-Type' => 'application/ld+json; charset=utf-8',
                    'Authorization' => 'Bearer ' . $userToken
                ]
            ])
            ->assertStatus(201);


        /******************************/
        /*****  HttpOptions::json *****/
        /******************************/
        $this->browser()
            ->post('/products', HttpOptions::json(
                [
                    'name' => 'test',
                    'quantity' => 5,
                    'price' => 2000.0,
                    'description' => 'test description',
                    'category' => '/categories/' . CategoryFactory::createOne()->getId(),
                    'supplier' => '/users/' . $user->getId()
                ]
            )
                ->withHeaders([
                    'Accept' => 'application/ld+json',
                    'Content-Type' => 'application/ld+json; charset=utf-8',
                    'Authorization' => 'Bearer ' . $userToken
                ])
            )
            ->assertStatus(201);


    }

    public function testPostNewProductAutoSupplier()
    {
        $user = UserFactory::createOne(['password' => '0000', 'roles' => ['ROLE_PRODUCT_CREATE']]);
        $userToken = $this->getUserToken($user);

        $this->browser()
            ->post('/products', [
                'json' => [
                    'name' => 'test',
                    'quantity' => 5,
                    'price' => 2000.0,
                    'description' => 'test description',
                    'category' => '/categories/' . CategoryFactory::createOne()->getId(),
                ],
                'headers' => [
                    'Accept' => 'application/ld+json',
                    'Content-Type' => 'application/ld+json; charset=utf-8',
                    'Authorization' => 'Bearer ' . $userToken
                ]
            ])
            ->assertStatus(201);

    }

    public function testPostNewProductDenied()
    {
        $user = UserFactory::createOne(['password' => '0000', 'roles' => ['ROLE_PRODUCT_EDIT']]);
        $userToken = $this->getUserToken($user);

        $result = $this->browser()
            ->post('/products', [
                'json' => [
                    'name' => 'test',
                    'quantity' => 5,
                    'price' => 2000.0,
                    'description' => 'test description',
                    'category' => '/categories/' . CategoryFactory::createOne()->getId(),
                    'supplier' => '/users/' . $user->getId()
                ],
                'headers' => [
                    'Accept' => 'application/ld+json',
                    'Content-Type' => 'application/ld+json; charset=utf-8',
                    'Authorization' => 'Bearer ' . $userToken
                ]
            ])
            ->assertStatus(403);
    }

    public function testPatchToUpdateProduct()
    {
        $user = UserFactory::createOne(['password' => '0000', 'roles' => ['ROLE_PRODUCT_EDIT']]);
//        $user = UserFactory::createOne();

        $category = CategoryFactory::createOne();

        $product = ProductFactory::createOne(
            [
                'supplier' => $user,
                'category' => $category,
            ]
        );

        $this->browser()
            ->actingAs($user)
            ->patch('/products/' . $product->getId(), [
                'json' => [
                    'quantity' => 500,
                ],
                'headers' => [
                    'Accept' => 'application/ld+json',
                    'Content-Type' => 'application/merge-patch+json; charset=utf-8',
                ]
            ])
            ->assertStatus(200)
            ->assertJsonMatches('quantity', 500);


        $user2 = UserFactory::createOne(['password' => '0000', 'roles' => ['ROLE_PRODUCT_EDIT']]);
        $this->browser()
            ->actingAs($user2)
            ->patch('/products/' . $product->getId(), [
                'json' => [
                    'quantity' => 500,
                ],
                'headers' => [
                    'Accept' => 'application/ld+json',
                    'Content-Type' => 'application/merge-patch+json; charset=utf-8',
                ]
            ])
            ->assertStatus(403);

        $user2 = UserFactory::createOne(['password' => '0000', 'roles' => ['ROLE_PRODUCT_EDIT']]);
        $this->browser()
            ->actingAs($user)
            ->patch('/products/' . $product->getId(), [
                'json' => [
                    'supplier' => '/users/' . $user2->getId(),
                ],
                'headers' => [
                    'Accept' => 'application/ld+json',
                    'Content-Type' => 'application/merge-patch+json; charset=utf-8',
                ]
            ])
//            ->assertStatus(403); -> validation error
            ->assertStatus(422); //-> invalid data

    }

    public function testAdminCanPatchToEditProduct()
    {
        $user = UserFactory::new()->asAdmin()->create();

        $product = ProductFactory::createOne([
            'isVerified' => false
        ]);

        $this->browser()
            ->actingAs($user)
            ->patch('/products/' . $product->getId(), [
                'json' => [
                    'quantity' => 500
                ],
                'headers' => [
                    'Accept' => 'application/ld+json',
                    'Content-Type' => 'application/merge-patch+json; charset=utf-8',
                ]
            ])
            ->assertStatus(200)
            ->assertJsonMatches('quantity', 500)
            ->assertJsonMatches('isVerified', false);


    }

    public function testSupplierSeeProductIsVerified()
    {
        $user = UserFactory::new()->withRoles(['ROLE_PRODUCT_EDIT'])->create();

        $product = ProductFactory::createOne([
            'isVerified' => false,
            'supplier' => $user,
        ]);

        $this->browser()
            ->actingAs($user)
            ->patch('/products/' . $product->getId(), [
                'json' => [
                    'quantity' => 500
                ],
                'headers' => [
                    'Accept' => 'application/ld+json',
                    'Content-Type' => 'application/merge-patch+json; charset=utf-8',
                ]
            ])
            ->assertStatus(200)
            ->assertJsonMatches('quantity', 500)
            ->assertJsonMatches('isVerified', false);;

    }

    public function testSupplierSeeProductIsVerifiedAndIsMine()
    {
        $user = UserFactory::new()->withRoles(['ROLE_PRODUCT_EDIT'])->create();

        $product = ProductFactory::createOne([
            'isVerified' => false,
            'supplier' => $user,
        ]);

        $this->browser()
            ->actingAs($user)
            ->patch('/products/' . $product->getId(), [
                'json' => [
                    'quantity' => 500
                ],
                'headers' => [
                    'Accept' => 'application/ld+json',
                    'Content-Type' => 'application/merge-patch+json; charset=utf-8',
                ]
            ])
            ->assertStatus(200)
            ->assertJsonMatches('quantity', 500)
            ->assertJsonMatches('isVerified', false)
            ->assertJsonMatches('isMine', true);

    }

    public function testPublishProduct()
    {
        $user = UserFactory::new()->withRoles(['ROLE_PRODUCT_EDIT'])->create();

        $product = ProductFactory::createOne([
            'isVerified' => false,
            'supplier' => $user,
        ]);

        $this->browser()
            ->actingAs($user)
            ->patch('/products/' . $product->getId(), [
                'json' => [
                    'isVerified' => true
                ],
                'headers' => [
                    'Accept' => 'application/ld+json',
                    'Content-Type' => 'application/merge-patch+json; charset=utf-8',
                ]
            ])
            ->assertStatus(200)
            ->assertJsonMatches('isVerified', true)
            ;

    }

}