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
    private ?gameUser $user_id = null;

    #[ORM\ManyToOne(inversedBy: 'libraryGames')]
    #[ORM\JoinColumn(nullable: false)]
    private ?game $game_id = null;

    #[ORM\ManyToOne(inversedBy: 'libraryGames')]
    #[ORM\JoinColumn(nullable: false)]
    private ?status $status_id = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $personal_rating = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $personal_review = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $sarted_at = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $finished_at = null;

    #[ORM\Column]
    private ?bool $is_favorite = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $added_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?gameUser
    {
        return $this->user_id;
    }

    public function setUserId(?gameUser $user_id): static
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getGameId(): ?game
    {
        return $this->game_id;
    }

    public function setGameId(?game $game_id): static
    {
        $this->game_id = $game_id;

        return $this;
    }

    public function getStatusId(): ?status
    {
        return $this->status_id;
    }

    public function setStatusId(?status $status_id): static
    {
        $this->status_id = $status_id;

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

    public function getSartedAt(): ?\DateTime
    {
        return $this->sarted_at;
    }

    public function setSartedAt(?\DateTime $sarted_at): static
    {
        $this->sarted_at = $sarted_at;

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
