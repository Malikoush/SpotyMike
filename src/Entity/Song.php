<?php

namespace App\Entity;

use App\Repository\SongRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SongRepository::class)]
class Song
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 90)]
    private ?string $idSong = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 125)]
    private ?string $url = null;

    #[ORM\Column(length: 125)]
    private ?string $cover = null;

    #[ORM\Column]
    private ?bool $visibility = true;

    #[ORM\Column]
    private ?\DateTimeImmutable $createAt = null;

    #[ORM\ManyToMany(targetEntity: Artist::class, inversedBy: 'songs')]
    private Collection $Artist_idUser;

    #[ORM\ManyToMany(targetEntity: Playlist::class, inversedBy: 'songs')]
    private Collection $playlist_idPlaylist;

    #[ORM\ManyToOne(inversedBy: 'song_idSong', cascade: ['persist', 'remove'])]
    private ?Album $album = null;

    public function __construct()
    {
        $this->Artist_idUser = new ArrayCollection();
        $this->playlist_idPlaylist = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdSong(): ?string
    {
        return $this->idSong;
    }

    public function setIdSong(string $idSong): static
    {
        $this->idSong = $idSong;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getCover(): ?string
    {
        return $this->cover;
    }

    public function setCover(string $cover): static
    {
        $this->cover = $cover;

        return $this;
    }

    public function isVisibility(): ?bool
    {
        return $this->visibility;
    }

    public function setVisibility(bool $visibility): static
    {
        $this->visibility = $visibility;

        return $this;
    }

    public function getCreateAt(): ?\DateTimeImmutable
    {
        return $this->createAt;
    }

    public function setCreateAt(\DateTimeImmutable $createAt): static
    {
        $this->createAt = $createAt;

        return $this;
    }

    /**
     * @return Collection<int, Artist>
     */
    public function getArtistIdUser(): Collection
    {
        return $this->Artist_idUser;
    }

    public function addArtistIdUser(Artist $artistIdUser): static
    {
        if (!$this->Artist_idUser->contains($artistIdUser)) {
            $this->Artist_idUser->add($artistIdUser);
        }

        return $this;
    }

    public function removeArtistIdUser(Artist $artistIdUser): static
    {
        $this->Artist_idUser->removeElement($artistIdUser);

        return $this;
    }

    /**
     * @return Collection<int, Playlist>
     */
    public function getPlaylistIdPlaylist(): Collection
    {
        return $this->playlist_idPlaylist;
    }

    public function addPlaylistIdPlaylist(Playlist $playlistIdPlaylist): static
    {
        if (!$this->playlist_idPlaylist->contains($playlistIdPlaylist)) {
            $this->playlist_idPlaylist->add($playlistIdPlaylist);
        }

        return $this;
    }

    public function removePlaylistIdPlaylist(Playlist $playlistIdPlaylist): static
    {
        $this->playlist_idPlaylist->removeElement($playlistIdPlaylist);

        return $this;
    }

    public function getAlbum(): ?Album
    {
        return $this->album;
    }

    public function setAlbum(?Album $album): static
    {
        $this->album = $album;

        return $this;
    }

    public function serializer()
    {
        return [
            "id" => $this->getId(),
            "title" => $this->getTitle(),
            // Ajoutez d'autres attributs de l'album si nécessaire
        ];
    }
}
