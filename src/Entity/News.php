<?php

namespace App\Entity;

use App\Repository\NewsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=NewsRepository::class)
 */
class News
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"get_news_item"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"get_news_item"})
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"get_news_item"})
     */
    private $content;

    /**
     * @ORM\Column(type="boolean")
     * 
     */
    private $isHomeEvent;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     * @Groups({"get_news_item"})
     */
    private $sliderPosition;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Groups({"get_news_item"})
     */
    private $publishedAt;

    /**
     * @ORM\OneToMany(targetEntity=Picture::class, mappedBy="news")
     * @Groups({"get_news_item"})
     */
    private $pictures;

    public function __construct()
    {
        $this->pictures = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function isIsHomeEvent(): ?bool
    {
        return $this->isHomeEvent;
    }

    public function setIsHomeEvent(bool $isHomeEvent): self
    {
        $this->isHomeEvent = $isHomeEvent;

        return $this;
    }

    public function getSliderPosition(): ?int
    {
        return $this->sliderPosition;
    }

    public function setSliderPosition(int $sliderPosition): self
    {
        $this->sliderPosition = $sliderPosition;

        return $this;
    }

    public function getPublishedAt(): ?\DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(\DateTimeImmutable $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    /**
     * @return Collection<int, Picture>
     */
    public function getPictures(): Collection
    {
        return $this->pictures;
    }

    public function addPicture(Picture $picture): self
    {
        if (!$this->pictures->contains($picture)) {
            $this->pictures[] = $picture;
            $picture->setNews($this);
        }

        return $this;
    }
    public function emptyPicture()
    {
        $this->pictures = new ArrayCollection();

        return $this;
    }

    public function removePicture(Picture $picture): self
    {
        if ($this->pictures->removeElement($picture)) {
            // set the owning side to null (unless already changed)
            if ($picture->getNews() === $this) {
                $picture->setNews(null);
            }
        }

        return $this;
    }
}
