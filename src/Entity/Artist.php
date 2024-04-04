<?php

namespace App\Entity;

use App\Repository\ArtistRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: ArtistRepository::class)]
class Artist
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 90)]
    private ?string $fullname = null;

    #[ORM\Column(length: 90)]
    private ?string $label = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updateAt = null;

    #[ORM\OneToOne(inversedBy: 'artist', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $User_idUser = null;

    #[ORM\OneToMany(targetEntity: Album::class, mappedBy: 'artist_User_idUser')]
    private Collection $album_idAlbum;


    public function __construct()
    {
        $this->album_idAlbum = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFullname(): ?string
    {
        return $this->fullname;
    }

    public function setFullname(string $fullname): static
    {
        $this->fullname = $fullname;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getUserIdUser(): ?User
    {
        return $this->User_idUser;
    }

    public function setUserIdUser(User $User_idUser): static
    {
        $this->User_idUser = $User_idUser;

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

    public function getUpdateAt(): ?\DateTimeInterface
    {
        return $this->updateAt;
    }

    public function setUpdateAt(\DateTimeInterface $updateAt): static
    {
        $this->updateAt = $updateAt;

        return $this;
    }

    /**
     * @return Collection<int, Album>
     */
    public function getAlbumIdAlbum(): Collection
    {
        return $this->album_idAlbum;
    }

    public function addAlbumIdAlbum(Album $albumIdAlbum): static
    {
        if (!$this->album_idAlbum->contains($albumIdAlbum)) {
            $this->album_idAlbum->add($albumIdAlbum);
            $albumIdAlbum->setArtistUserIdUser($this);
        }

        return $this;
    }

    public function removeAlbumIdAlbum(Album $albumIdAlbum): static
    {
        if ($this->album_idAlbum->removeElement($albumIdAlbum)) {
            // set the owning side to null (unless already changed)
            if ($albumIdAlbum->getArtistUserIdUser() === $this) {
                $albumIdAlbum->setArtistUserIdUser(null);
            }
        }

        return $this;
    }

    public function serializer($children = false)
    {
        $albumsData = [];
        $albums = $this->getAlbumIdAlbum();
        foreach ($albums as $album) {
            $albumsData[] = $album->serializer();
        }

        return [
            "error" => false,
            "firstname" => $this->getUserIdUser()->getFirstname(),
            "lastname" => $this->getUserIdUser()->getLastname(),
            "sexe" => $this->getUserIdUser()->getSexe(),
            "dateBirth" => $this->getUserIdUser()->getDateBirth()->format('Y-m-d H:i:s'),
            "Artist.createdAt" => $this->getCreateAt()->format('Y-m-d H:i:s'),
            "album" => $albumsData,
        ];
    }
}
