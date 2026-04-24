<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ShoppingListApiTest extends WebTestCase
{
    private function getAuthHeaders($client, $username = 'api_user'): array
    {
        $name = $username . bin2hex(random_bytes(2));
        $client->request('POST', '/api/register', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'name' => $name,
            'password' => 'testpass123'
        ]));

        $client->request('POST', '/api/login_check', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'username' => $name,
            'password' => 'testpass123'
        ]));

        $data = json_decode($client->getResponse()->getContent(), true);
        return [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $data['token'],
            'CONTENT_TYPE' => 'application/json'
        ];
    }

    public function testCreateAndGetLists(): void
    {
        $client = static::createClient();
        $headers = $this->getAuthHeaders($client);

        // 1. Liste erstellen
        $client->request('POST', '/api/lists', [], [], $headers, json_encode([
            'name' => 'Mein Test-Einkauf'
        ]));
        $this->assertResponseStatusCodeSame(201);
        $listData = json_decode($client->getResponse()->getContent(), true);
        $listId = $listData['id'];

        // 2. Listen abrufen
        $client->request('GET', '/api/lists', [], [], $headers);
        $this->assertResponseStatusCodeSame(200);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertCount(1, $data);
        $this->assertSame('Mein Test-Einkauf', $data[0]['name']);
    }

    public function testItemCrud(): void
    {
        $client = static::createClient();
        $headers = $this->getAuthHeaders($client, 'item_user');

        // 1. Liste erstellen
        $client->request('POST', '/api/lists', [], [], $headers, json_encode(['name' => 'Item Liste']));
        $listId = json_decode($client->getResponse()->getContent(), true)['id'];

        // 2. Item hinzufügen
        $client->request('POST', "/api/lists/$listId/items", [], [], $headers, json_encode([
            'name' => 'Bananen',
            'amount' => 5
        ]));
        $this->assertResponseStatusCodeSame(201);
        
        // 3. Item updaten
        $data = json_decode($client->getResponse()->getContent(), true);
        $itemId = $data['items'][0]['id'];
        
        $client->request('PUT', "/api/lists/items/$itemId", [], [], $headers, json_encode([
            'state' => 'done'
        ]));
        $this->assertResponseStatusCodeSame(200);
        $updatedData = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('done', $updatedData['state']);
    }
}
