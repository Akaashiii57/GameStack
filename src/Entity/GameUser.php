<?php

namespace App\Entity;

use App\Repository\GameUserRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: GameUserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class GameUser implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $Username = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $lastActivityAt = null;

    #[ORM\OneToOne(targetEntity: SteamAccount::class, mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?SteamAccount $steamAccount = null;

    /**
     * @var Collection<int, LibraryGame>
     */
    #[ORM\OneToMany(targetEntity: LibraryGame::class, mappedBy: 'user')]
    private Collection $libraryGames;

    public function __construct()
    {
        $this->libraryGames = new ArrayCollection();
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): ?string
    {
        return $this->Username;
    }

    public function setUsername(string $Username): static
    {
        $this->Username = $Username;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getLastActivityAt(): ?DateTimeImmutable
    {
        return $this->lastActivityAt;
    }

    public function setLastActivityAt(?DateTimeImmutable $lastActivityAt): static
    {
        $this->lastActivityAt = $lastActivityAt;

        return $this;
    }

    /**
     * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0" . self::class . "\0password"] = hash('crc32c', $this->password);

        return $data;
    }

    /**
     * @return Collection<int, LibraryGame>
     */
    public function getLibraryGames(): Collection
    {
        return $this->libraryGames;
    }

    public function addLibraryGame(LibraryGame $libraryGame): static
    {
        if (!$this->libraryGames->contains($libraryGame)) {
            $this->libraryGames->add($libraryGame);
            $libraryGame->setUser($this);
        }

        return $this;
    }

    public function removeLibraryGame(LibraryGame $libraryGame): static
    {
        if ($this->libraryGames->removeElement($libraryGame)) {
            if ($libraryGame->getUser() === $this) {
                $libraryGame->setUser(null);
            }
        }

        return $this;
    }

    public function getSteamAccount(): ?SteamAccount
    {
        return $this->steamAccount;
    }

    public function setSteamAccount(?SteamAccount $steamAccount): static
    {
        // Définir la propriété owning side si nécessaire
        if ($steamAccount && $steamAccount->getUser() !== $this) {
            $steamAccount->setUser($this);
        }

        $this->steamAccount = $steamAccount;

        return $this;
    }
}
