<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Entity\Company;
use App\Entity\User;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\ORM\EntityManagerInterface;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

abstract class AbstractTest extends ApiTestCase
{
    use RefreshDatabaseTrait;

    private ?string $token = null;
    protected ?EntityManagerInterface $entityManager = null;

    public function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $this->loadFixtures();
    }

    protected function loadFixtures(): void
    {
        $container = self::$kernel->getContainer();
        $loader = new Loader();
        $loader->addFixture($container->get(\App\DataFixtures\UserFixtures::class));
        $loader->addFixture($container->get(\App\DataFixtures\AppFixtures::class));
        $purger = new ORMPurger();
        $executor = new ORMExecutor($container->get('doctrine')->getManager(), $purger);
        $executor->execute($loader->getFixtures());
    }
    protected function createClientWithCredentials($token = null): Client
    {
        $token = $token ?: $this->getToken();

        return static::createClient([], ['headers' => ['Authorization' => 'Bearer ' . $token]]);
    }

    protected function getToken($body = []): string
    {
        if ($this->token) {
            return $this->token;
        }
        $headers[] = 'Content-Type: application/json';
        $client = static::createClient();
        $response = $client->request('POST', '/api/login_check', [
            'json' => $body,
            'headers' => $headers,
            'timeout' => 120.0,
        ]);

        $data = $response->toArray();
        $this->token = $data['token'];

        return $data['token'];
    }

    protected function getUserByEmail(string $email)
    {
        return $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
    }

    protected function getCompanyByName(string $name)
    {
        return $this->entityManager->getRepository(Company::class)->findOneBy(['name' => $name]);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
