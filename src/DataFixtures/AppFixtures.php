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
        $states = [
            'PENDING'=>'pending',
            'VALIDATED'=>'validated',
            'REFUSED'=>'Refused',
            'CANCELED'=>'Canceled',
            'IN_PROGRESS'=>'In Progress',
            'COMPLETED'=>'Completed',
        ];
        foreach ($states as $key => $stateName) {
            $state = new Status();
            $state->setName($stateName);
            $state->setTechName($key);
            $manager->persist($state);
        }

        $statuses = [
            'IN_PROGRESS'=>'En cours',
            'CANCELED'=>'Annulé',
            'REPORTED'=>'Reporté',

        ];
        foreach ($statuses as $key => $statusName) {
            $status = new State();
            $status->setName($statusName);
            $status->setTechName($key);
            $manager->persist($status);
        }

        // //création du super admin
        // $admin = new \App\Entity\User();
        // $admin->setEmail('admin@sis.com');
        // $admin->setPassword('admin');
        // $admin->setRoles(['ROLE_SUPER_ADMIN']);
        // $admin->setFirstName('Admin');
        // $admin->setLastName('Admin');
        // $admin->setNickname('admin@001');
        // $admin->setTel('1234567890');
        // $admin->setAddress('123 Admin St, Admin City, Admin Country');
        // $admin->setBirth(new \DateTime('2000-01-01'));
        // $admin->setGender('M');
        // $manager->persist($admin);

        $manager->flush();
    }
}
