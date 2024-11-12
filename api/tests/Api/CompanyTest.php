<?php

namespace App\Tests\Api;

use App\Tests\AbstractTest;

final class CompanyTest extends AbstractTest
{

    public function testAdminResource()
    {
        $this->assertTrue(true);
    }

    /**
     * Test get company collection
     */
    public function testGetCollection(): void
    {
        $token = $this->getToken([
            'email' => 'naseriimahmoud+2@gmail.com',
            'password' => 'Mana@123',
        ]);

        $response = $this->createClientWithCredentials($token)->request('GET', '/api/v1/companies');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/contexts/Company',
            '@id' => '/api/v1/companies',
            '@type' => 'hydra:Collection',
        ]);

        $this->assertCount(30, $response->toArray()['hydra:member']);
    }

    /**
     * Test get company detail
     */
    public function testGetCompany(): void
    {
        $token = $this->getToken([
            'email' => 'naseriimahmoud+2@gmail.com',
            'password' => 'Mana@123',
        ]);

        $company = $this->getCompanyByName('Future Driver App');
        $companyId = $company->getId();

        $response = $this->createClientWithCredentials($token)->request('GET', '/api/v1/companies/'.$companyId);
        $this->assertResponseIsSuccessful();

        $data = $response->toArray();
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('name', $data);
    }

    /**
     * Test create new company
     */
    public function testCreateCompany(): void
    {
        $token = $this->getToken([
            'email' => 'naseriimahmoud+2@gmail.com',
            'password' => 'Mana@123',
        ]);

        $headers[] = 'Content-Type: application/ld+json';
        $response = $this->createClientWithCredentials($token)->request('POST', '/api/v1/companies', [
            'json' => [
                'name' => 'New Company'
            ],
            'headers' => $headers
        ]);

        $this->assertResponseStatusCodeSame(201);

        $data = $response->toArray();
        $this->assertArrayHasKey('id', $data);
        $this->assertEquals('New Company', $data['name']);
    }

    /**
     * Test unauthorized access
     */
    public function testUnauthorizedAccess(): void
    {
        $client = static::createClient();
        $response = $client->request('GET', '/api/v1/companies');
        $this->assertResponseStatusCodeSame(401);
    }

    /**
     * Test create company by not super admin credential
     */
    public function testUnauthorizedCreateCompany(): void
    {
        $token = $this->getToken([
            'email' => 'naseriimahmoud+1@gmail.com',
            'password' => 'Mana@123',
        ]);

        $headers[] = 'Content-Type: application/ld+json';
        $response = $this->createClientWithCredentials($token)->request('POST', '/api/v1/companies', [
            'json' => [
                'name' => 'Unauthorized Company'
            ],
            'headers' => $headers
        ]);

        $this->assertResponseStatusCodeSame(403);
    }
}
