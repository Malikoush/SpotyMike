<?php

namespace App\DataFixtures;

use App\Entity\Album;
use App\Entity\Artist;
use App\Entity\User;
use App\Entity\Song;
use App\Entity\Playlist;
use App\Entity\Label;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i < 8; $i++) {
            
            // Add New User
            $user = new User;
            $user->setIdUser($i);
            $sexe = rand(0, 1) === 0 ? "Homme" : "Femme";
            $user->setSexe($sexe);
            $user->setFirstname("User_" . $i);
            $user->setLastname("User_" . $i);
            $user->setEmail("user_" . $i . "@gmail.com");
            $user->setPassword("User_" . $i);
            $user->setDateBirth(new DateTimeImmutable());
            $user->setCreateAt(new DateTimeImmutable());
            $user->setUpdateAt(new DateTimeImmutable());
            $manager->persist($user);
                
            // Add New Artist
            $artist = new Artist;
            $manager->persist($artist);
            $artist->setFullname("Artist_" . $i);
            $artist->setUserIdUser($user);
            $artist->setDescription("Artist_" . $i);
            $artist->setCreateAt(new DateTimeImmutable());
            $artist->setUpdateAt(new DateTimeImmutable());

            // Add New Label
            $label = new Label;
            $manager->persist($label);
            $label->setNom("Label_" . $i);
            $label->setCreateAt(new DateTimeImmutable());
            $label->setUpdateAt(new DateTimeImmutable());  

            // Add New Album
            $album = new Album;
            $album->setIdAlbum($i);
            $album->setArtistUserIdUser($artist);
            $album->setNom("Album_" . $i);
            $album->setCateg("Album_" . $i);
            $album->setCover("Album_" . $i);
            $album->setYear(rand(1900, 2024));
            $album->setCreateAt(new DateTimeImmutable());
            $album->setUpdateAt(new DateTimeImmutable());
            $manager->persist($album);

            // Add New Song
            $song = new Song;
            $song->setIdSong($i);
            $song->setTitle("Song_" . $i);
            $song->setUrl("Song_" . $i);
            $song->setCover("Song_" . $i);
            $song->setVisibility(rand(0, 1));
            $song->setCreateAt(new DateTimeImmutable());
            $song->setAlbum($album);
            $manager->persist($song);

            // Add New Playlist
            $playlist = new Playlist;
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
