<?php

namespace App\DataFixtures;

use App\Entity\Event;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');


        //add Event
        for ($i = 0; $i < 20; $i++) {
            $event = new Event();
            $event->setTitle('concert' . $i)
                ->setDescription($faker->text())
                ->setStartDate($faker->dateTimeBetween('now', '+15 days'))
                ->setEndDate($faker->dateTimeBetween('+16 days', '+30 days'))
                ->setPrice(rand(0, 1) == 1 ? rand(10, 50) : null);

            $manager->persist($event);
        }

        $manager->flush();
    }
}
