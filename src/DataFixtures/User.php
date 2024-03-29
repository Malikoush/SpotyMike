<?php

namespace App\DataFixtures;

use App\Entity\Artist;
use App\Entity\User as EntityUser;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\Persistence\ObjectManager;

class User extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 7; $i++) {
            # code...
            $user = new EntityUser;
            $manager->persist($user);
            $user->setIdUser($i);
            $sexe = rand(0, 1) === 0 ? "Homme" : "Femme";
            $user->setSexe($sexe);
            $user->setFirstname("User_" . $i);
            $user->setLastname("User_" . $i);
            $user->setEmail("User_" . $i);
            $user->setDateBirth(new DateTimeImmutable());
            $user->setCreateAt(new DateTimeImmutable());
            $user->setUpdateAt(new DateTimeImmutable());
            $user->setPassword("User_" . $i);
            if (rand(0, 1)) {
                $artist = new Artist();
            }
        }
        $manager->flush();
    }
}
