<?php

namespace App\Entity;

use App\Repository\CardRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=CardRepository::class)
 */
class Card
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="smallint")
     * @Groups({"users_get_item"})
     */
    private $amount;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Groups({"users_get_item"})
     */
    private $boughtAt;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Groups({"users_get_item"})
     */
    private $limitedDate;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     * @Groups({"users_get_item"})
     */
    private $usedAt;

    /**
     * @ORM\Column(type="string", length=80)
     * @Groups({"users_get_item"})
     */
    private $gifter;

    /**
     * @ORM\Column(type="string", length=80)
     * @Groups({"users_get_item"})
     */
    private $receiver;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="cards")
     * @ORM\JoinColumn(nullable=true)
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=6, nullable=true)
     */
    private $reference;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $tel;

    public function __construct()
    {
        $this->boughtAt = new DateTimeImmutable();
        $this->limitedDate = new DateTimeImmutable('+1 year');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getBoughtAt(): ?\DateTimeImmutable
    {
        return $this->boughtAt;
    }

    public function setBoughtAt(\DateTimeImmutable $boughtAt): self
    {
        $this->boughtAt = $boughtAt;

        return $this;
    }

    public function getLimitedDate(): ?\DateTimeImmutable
    {
        return $this->limitedDate;
    }

    public function setLimitedDate(\DateTimeImmutable $limitedDate): self
    {
        $this->limitedDate = $limitedDate;

        return $this;
    }

    public function getUsedAt(): ?\DateTimeImmutable
    {
        return $this->usedAt;
    }

    public function setUsedAt(\DateTimeImmutable $usedAt): self
    {
        $this->usedAt = $usedAt;

        return $this;
    }

    public function getGifter(): ?string
    {
        return $this->gifter;
    }

    public function setGifter(string $gifter): self
    {
        $this->gifter = $gifter;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get the value of receiver
     */ 
    public function getReceiver()
    {
        return $this->receiver;
    }

    /**
     * Set the value of receiver
     *
     * @return  self
     */ 
    public function setReceiver($receiver)
    {
        $this->receiver = $receiver;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
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

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(?string $tel): self
    {
        $this->tel = $tel;

        return $this;
    }
}
