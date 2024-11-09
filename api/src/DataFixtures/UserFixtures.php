<?php

namespace App\DataFixtures;

use App\Entity\Company;
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
        $company = new Company();
        $company->setName('Future Driver');
        $manager->persist($company);

        $appCompany = new Company();
        $appCompany->setName('Future Driver App');
        $manager->persist($appCompany);

        $user = new User();
        $user->setEmail('naseriimahmoud@gmail.com')
            ->setRoles(['ROLE_USER'])
            ->setName('Mahmoud Naseri')
            ->setCompany($company)
            ->setPassword($this->passwordHasher->hashPassword($user, 'Mana@123'));
        $manager->persist($user);

        $user = new User();
        $user->setEmail('naseriimahmoud+1@gmail.com')
            ->setRoles(['ROLE_COMPANY_ADMIN'])
            ->setName('Mahmoud Company Admin')
            ->setCompany($company)
            ->setPassword($this->passwordHasher->hashPassword($user, 'Mana@123'));
        $manager->persist($user);

        $user = new User();
        $user->setEmail('naseriimahmoud+2@gmail.com')
            ->setRoles(['ROLE_SUPER_ADMIN'])
            ->setName('Mahmoud SUPER Admin')
            ->setPassword($this->passwordHasher->hashPassword($user, 'Mana@123'));
        $manager->persist($user);

        $manager->flush();
    }
}
