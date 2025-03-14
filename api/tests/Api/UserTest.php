<?php

namespace App\Tests\Api;

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
}