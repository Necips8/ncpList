<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiControllerTest extends WebTestCase
{
    public function testRegistration(): void
    {
        $client = static::createClient();
        
        // Zufälliger Name um Konflikte zu vermeiden falls DB nicht zurückgesetzt wird
        $name = 'user_' . bin2hex(random_bytes(4));

        $client->request(
            'POST',
            '/api/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'name' => $name,
                'password' => 'testpass123'
            ])
        );

        $this->assertResponseStatusCodeSame(201);
        $this->assertJson($client->getResponse()->getContent());
        $this->assertStringContainsString('User registered successfully', $client->getResponse()->getContent());
    }

    public function testLogin(): void
    {
        $client = static::createClient();
        
        $name = 'login_user_' . bin2hex(random_bytes(4));

        // 1. Registrieren
        $client->request('POST', '/api/register', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'name' => $name,
            'password' => 'testpass123'
        ]));

        // 2. Einloggen
        $client->request(
            'POST',
            '/api/login_check',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'username' => $name,
                'password' => 'testpass123'
            ])
        );

        $this->assertResponseStatusCodeSame(200);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('token', $data);
    }
}
