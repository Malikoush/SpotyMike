<?php

namespace App\DataFixtures;

use App\Entity\Album;
use App\Entity\Song as EntitySong;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SongFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {   
        for ($i = 1; $i < 8; $i++) {
            $album = new Album();
            $manager->persist($album);
            $song = new EntitySong;
            $manager->persist($song);
            $song->setIdSong($i);
            $song->setTitle("Song_" . $i);
            $song->setUrl("Song_" . $i);
            $song->setCover("Song_" . $i);
            $song->setVisibility(rand(0, 1));
            $song->setCreateAt(new DateTimeImmutable());
            $song->setAlbum($album);
        }
        $manager->flush();
    }
}
