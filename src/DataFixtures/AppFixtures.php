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
        //insertion des States

        $states = [
            'PENDING'=>'Pending',
            'VALIDATED'=>'Validated',
            'REFUSED'=>'Refused',
            'CANCELED'=>'Canceled',
            'IN_PROGRESS'=>'In Progress',
            'COMPLETED'=>'Completed',
        ];
        foreach ($states as $key => $stateName) {
            $state = new State();
            $state->setName($stateName);
            $state->setTechName($key);
            $manager->persist($state);
        }

        $statuses = [
            'EN_COURS'=>'En cours',
            'REPPORTE'=>'Reporté',
            'TERMINE'=>'Terminé',
            'ANNULE'=>'Annulé',
            'EN_ATTENTE'=>'En attente',
            'EN_ATTENTE_DE_CONFIRMATION'=>'En attente de confirmation',
            'EN_ATTENTE_DE_RESULTATS'=>'En attente de résultats',
            'CONFIRME'=>'Confirmé',
        ];
        foreach ($statuses as $key => $statusName) {
            $status = new Status();
            $status->setName($statusName);
            // $status->setTechName($key);
            $manager->persist($status);
        }

        //création du super admin
        $admin = new \App\Entity\User();
        $admin->setEmail('admin@sis.com');
        $admin->setPassword('admin');
        $admin->setRoles(['ROLE_SUPER_ADMIN']);
        $admin->setFirstName('Admin');
        $admin->setLastName('Admin');
        $admin->setNickname('admin@001');
        $admin->setTel('1234567890');
        $admin->setAddress('123 Admin St, Admin City, Admin Country');
        $admin->setBirth(new \DateTime('2000-01-01'));
        $admin->setGender('M');
        $manager->persist($admin);
        
        $manager->flush();
    }
}
