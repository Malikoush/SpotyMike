<?php

namespace App\DataFixtures;

use App\Entity\Label as EntityLabel;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class LabelFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i < 8; $i++) {
            $label = new EntityLabel;
            $manager->persist($label);
            $label->setNom("Label_" . $i);
            $label->setCreateAt(new DateTimeImmutable());
            $label->setUpdateAt(new DateTimeImmutable());   
        }
        $manager->flush();
    }
}
