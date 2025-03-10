<?php

namespace App\Tests\Api;

use App\Factory\CategoryFactory;
use App\Factory\UserFactory;
use Zenstruck\Browser\HttpOptions;

class ProductTest extends ApiTestCase
{
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


}