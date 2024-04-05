<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Playlist as EntityPlaylist;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PlaylistFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i < 8; $i++) {
            $user = new User();
            $manager->persist($user);
            $playlist = new EntityPlaylist;
            $manager->persist($playlist);
            $playlist->setIdPlaylist($i);
            $playlist->setUser($user);
            $playlist->setTitle("Playlist_" . $i);
            $playlist->setPublic(rand(0, 1));
            $playlist->setCreateAt(new DateTimeImmutable());
            $playlist->setUpdateAt(new DateTimeImmutable());   
        }
        $manager->flush();
    }
}
