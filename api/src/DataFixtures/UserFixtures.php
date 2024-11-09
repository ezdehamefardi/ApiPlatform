<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('naseriimahmoud@gmail.com')
            ->setRoles(['ROLE_USER'])
            ->setName('Mahmoud Naseri')
            ->setPassword($this->passwordHasher->hashPassword($user, 'Mana@123'));

        $manager->persist($user);
        $manager->flush();
    }
}
