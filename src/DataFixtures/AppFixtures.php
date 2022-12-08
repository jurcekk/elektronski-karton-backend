<?php

namespace App\DataFixtures;

use App\Entity\HealthRecord;
use App\Entity\User;
use App\Factory\ExaminationFactory;
use App\Factory\HealthRecordFactory;
use App\Factory\PetFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        UserFactory::createMany(10);
        PetFactory::createMany(10);
        ExaminationFactory::createMany(4);

        HealthRecordFactory::createMany(20);
    }
}
