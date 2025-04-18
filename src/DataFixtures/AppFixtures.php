<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\State;
use App\Entity\Status;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $statuses = [
            'E',
            'En cours',
            'Terminé',
            'Annulé',
        ];
        $manager->flush();
    }
}
