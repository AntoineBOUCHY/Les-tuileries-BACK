<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100, unique=true)
     * @Groups({"users_get_item"})
     * @Assert\Email
     * @Assert\NotBlank
     */
    private $email;

    /**
     * @var string The hashed password
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Assert\Length(
     *      max=32
     * )
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=100)
     * @Groups({"users_get_item"})
     * @Assert\NotBlank
     * @Assert\Length(
     *      max=100
     * )
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=100)
     * @Groups({"users_get_item"})
     * @Assert\NotBlank
     * @Assert\Length(
     *      max=100
     * )
     */
    private $lastname;

    /**
     * @ORM\Column(type="json")
     * @Assert\Count(min=1, max=1)
     */
    private $role = [];

    /**
     * @ORM\Column(type="string", length=10)
     * @Groups({"users_get_item"})
     * @Assert\Length(
     *      min=10,
     *      max=10
     * )
     */
    private $phone;

    /**
     * @ORM\OneToMany(targetEntity=Card::class, mappedBy="user")
     * @Groups({"users_get_item"})
     */
    private $cards;

    public function __construct()
    {
        $this->role = ['ROLE_USER'];
        $this->cards = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstName): self
    {
        $this->firstname = $firstName;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $role = $this->role;
        // The role is in the DB
        // $role[] = 'ROLE_USER';

        return array_unique($role);
    }

    public function setRoles(array $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return Collection<int, Card>
     */
    public function getCards(): Collection
    {
        return $this->cards;
    }

    public function addCard(Card $card): self
    {
        if (!$this->cards->contains($card)) {
            $this->cards[] = $card;
            $card->setUser($this);
        }

        return $this;
    }

    public function removeCard(Card $card): self
    {
        if ($this->cards->removeElement($card)) {
            // set the owning side to null (unless already changed)
            if ($card->getUser() === $this) {
                $card->setUser(null);
            }
        }

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
