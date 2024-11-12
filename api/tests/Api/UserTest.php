<?php

namespace App\Tests\Api;

use App\Tests\AbstractTest;

final class UserTest extends AbstractTest
{
    public function testAdminResource()
    {
        $this->assertTrue(true);
    }

    /**
     * Test get user collection
     */
    public function testGetCollection(): void
    {
        $token = $this->getToken([
            'email' => 'naseriimahmoud+2@gmail.com',
            'password' => 'Mana@123',
        ]);

        $this->assertNotEmpty($token);

        $response = $this->createClientWithCredentials($token)->request('GET', '/api/v1/users');

        $this->assertResponseIsSuccessful();

        $this->assertResponseHeaderSame('Content-Type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/contexts/User',
            '@id' => '/api/v1/users',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 8,
        ]);

        $this->assertCount(8, $response->toArray()['hydra:member']);
    }

    /**
     * Test get user details.
     */
    public function testGetUser(): void
    {
        $token = $this->getToken([
            'email' => 'naseriimahmoud+2@gmail.com',
            'password' => 'Mana@123',
        ]);

        $user = $this->getUserByEmail('naseriimahmoud@gmail.com');
        $userId = $user->getId();

        // Fetch details for a specific user
        $response = $this->createClientWithCredentials($token)->request('GET', '/api/v1/users/'.$userId);
        $this->assertResponseIsSuccessful();

        $data = $response->toArray();
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('name', $data);
        $this->assertArrayHasKey('roles', $data);
        $this->assertArrayHasKey('email', $data);
    }

    /**
     * Test unauthorized access to user list without token.
     */
    public function testUnauthorizedAccess()
    {
        $client = static::createClient();
        $response = $client->request('GET', '/api/v1/users');
        $this->assertResponseStatusCodeSame(401);
    }

    /**
     * Test create new user.
     */
    public function testCreateUser()
    {
        $token = $this->getToken([
            'email' => 'naseriimahmoud+2@gmail.com',
            'password' => 'Mana@123',
        ]);

        $headers[] = 'Content-Type: application/ld+json';
        $client = $this->createClientWithCredentials($token);
        $response = $client->request('POST', '/api/v1/users', [
            'json' => [
                'name' => 'Mahmoud',
                'email' => 'test@mahmoud.com',
                'roles' => ['ROLE_USER'],
                'password' => 'Mana@123'
            ],
            'headers' => $headers
        ]);

        $this->assertResponseStatusCodeSame(201);
        $data = $response->toArray();
        $this->assertArrayHasKey('id', $data);
        $this->assertEquals('test@mahmoud.com', $data['email']);
    }

    /**
     * Test access to Super admin actions
     */
    public function testAccessNoneSuperAdminToOtherCompanyUser()
    {
        //none super admin user
        $token = $this->getToken([
            'email' => 'naseriimahmoud+1@gmail.com',
            'password' => 'Mana@123',
        ]);

        //This is none company user
        $user = $this->getUserByEmail('naseriimahmoud+2@gmail.com');
        $userId = $user->getId();

        $response = $this->createClientWithCredentials($token)->request('GET', '/api/v1/users/'.$userId);

        $this->assertResponseStatusCodeSame(404);
    }

    /**
     * Test delete a user as super admin
     */
    public function testDeleteUserAsSuperAdmin()
    {
        $token = $this->getToken([
            'email' => 'naseriimahmoud+2@gmail.com',
            'password' => 'Mana@123',
        ]);

        $user = $this->getUserByEmail('naseriimahmoud@gmail.com');
        $userId = $user->getId();

        $deleteResponse = $this->createClientWithCredentials($token)->request('DELETE', '/api/v1/users/'.$userId);

        $this->assertResponseStatusCodeSame(204);
    }

    /**
     * Test access without enough role
     */
    public function testRoleBasedAccess()
    {
        $token = $this->getToken([
            'email' => 'naseriimahmoud@gmail.com',
            'password' => 'Mana@123',
        ]);

        $headers[] = 'Content-Type: application/ld+json';
        $response = $this->createClientWithCredentials($token)->request('POST', '/api/v1/users', [
            'json' => [
                'name' => 'Mahmoud',
                'email' => 'testNew@mahmoud.com',
                'roles' => ['ROLE_USER'],
                'password' => 'Mana@123'
            ],
            'headers' => $headers
        ]);

        $this->assertResponseStatusCodeSame(403);
    }
}
