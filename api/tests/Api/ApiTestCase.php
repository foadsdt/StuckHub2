<?php

namespace App\Tests\Api;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Browser\HttpOptions;
use Zenstruck\Browser\KernelBrowser;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

abstract class ApiTestCase extends KernelTestCase
{
    use ResetDatabase, Factories;

    use HasBrowser {
        browser as BrowserHasBrowser;
    }

    protected function browser(array $options = [], array $server = []): KernelBrowser
    {
        return $this->BrowserHasBrowser($options, $server)
            ->setDefaultHttpOptions(HttpOptions::create()
//                ->withHeader('Content-Type', 'application/ld+json'))
//                ->withHeaders([
//                    'Accept' => 'application/ld+json',
//                    'Content-Type' => 'application/ld+json; charset=utf-8'
//                ])
            );
    }

    protected function getUserToken(User $user)
    {
        $token = $this->browser()
            ->post('/auth', [
                'json' => [
                    'email' => $user->getEmail(),
                    'password' => '0000',
                ]
            ])
            ->json()->decoded();
        return $token['token'];
    }


}