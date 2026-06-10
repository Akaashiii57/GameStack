<?php

namespace App\Entity;

use App\Repository\LibraryGameRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LibraryGameRepository::class)]
class LibraryGame
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'libraryGames')]
    #[ORM\JoinColumn(nullable: false)]
    private ?GameUser $user = null;

    #[ORM\ManyToOne(inversedBy: 'libraryGames')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Game $game = null;

    #[ORM\Column(length: 50)]
    private ?string $status = 'souhaite';

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $personal_rating = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $personal_review = null;

    #[ORM\Column(nullable: true)]
    private ?int $playtime = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $started_at = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $finished_at = null;

    #[ORM\Column]
    private ?bool $is_favorite = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $added_at = null;

    public function __construct()
    {
        $this->is_favorite = false;
        $this->added_at = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?GameUser
    {
        return $this->user;
    }

    public function setUser(?GameUser $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getGame(): ?game
    {
        return $this->game;
    }

    public function setGame(?Game $game): static
    {
        $this->game = $game;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getPersonalRating(): ?int
    {
        return $this->personal_rating;
    }

    public function setPersonalRating(?int $personal_rating): static
    {
        $this->personal_rating = $personal_rating;

        return $this;
    }

    public function getPersonalReview(): ?string
    {
        return $this->personal_review;
    }

    public function setPersonalReview(?string $personal_review): static
    {
        $this->personal_review = $personal_review;

        return $this;
    }

    public function getPlaytime(): ?int
    {
        return $this->playtime;
    }

    public function setPlaytime(?int $playtime): static
    {
        $this->playtime = $playtime;

        return $this;
    }

    public function getStartedAt(): ?\DateTime
    {
        return $this->started_at;
    }

    public function setStartedAt(?\DateTime $started_at): static
    {
        $this->started_at = $started_at;

        return $this;
    }

    public function getFinishedAt(): ?\DateTime
    {
        return $this->finished_at;
    }

    public function setFinishedAt(?\DateTime $finished_at): static
    {
        $this->finished_at = $finished_at;

        return $this;
    }

    public function isFavorite(): ?bool
    {
        return $this->is_favorite;
    }

    public function setIsFavorite(bool $is_favorite): static
    {
        $this->is_favorite = $is_favorite;

        return $this;
    }

    public function getAddedAt(): ?\DateTime
    {
        return $this->added_at;
    }

    public function setAddedAt(\DateTime $added_at): static
    {
        $this->added_at = $added_at;

        return $this;
    }
}
