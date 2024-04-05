<?php

namespace App\DataFixtures;

use App\Entity\Artist;
use App\Entity\Album as EntityAlbum;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AlbumFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i < 8; $i++) {
            $artist = new Artist();
            $manager->persist($artist);
            $album = new EntityAlbum;
            $manager->persist($album);
            $album->setIdAlbum($i);
            $album->setArtistUserIdUser($artist);
            $album->setNom("Album_" . $i);
            $album->setCateg("Album_" . $i);
            $album->setCover("Album_" . $i);
            $album->setYear(rand(1900, 2024));
            $album->setCreateAt(new DateTimeImmutable());
            $album->setUpdateAt(new DateTimeImmutable());   
        }
        $manager->flush();
    }
}
