<?php

namespace App\DataFixtures;

use App\Story\DefaultCompaniesStory;
use App\Story\DefaultUsersStory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        DefaultCompaniesStory::load();
        DefaultUsersStory::load();

        $manager->flush();
    }
}
