<?php

namespace App\Entity;

use App\Repository\PictureRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=PictureRepository::class)
 */
class Picture
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"get_news_item"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"get_news_item"})
     */
    private $path;

    /**
     * @ORM\ManyToMany(targetEntity=PageContent::class, inversedBy="pictures")
     */
    private $pageContents;

    /**
     * @ORM\ManyToOne(targetEntity=News::class, inversedBy="pictures")
     * @ORM\JoinColumn(onDelete="CASCADE") 
     */
    private $news;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"get_news_item"})
     */
    private $alt;

    public function __construct()
    {
        $this->pageContents = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return Collection<int, PageContent>
     */
    public function getPageContents(): Collection
    {
        return $this->pageContents;
    }

    public function addPageContent(PageContent $pageContent): self
    {
        if (!$this->pageContents->contains($pageContent)) {
            $this->pageContents[] = $pageContent;
        }

        return $this;
    }

    public function removePageContent(PageContent $pageContent): self
    {
        $this->pageContents->removeElement($pageContent);

        return $this;
    }

    public function getNews(): ?News
    {
        return $this->news;
    }

    public function setNews(?News $news): self
    {
        $this->news = $news;

        return $this;
    }

    public function getAlt(): ?string
    {
        return $this->alt;
    }

    public function setAlt(?string $alt): self
    {
        $this->alt = $alt;

        return $this;
    }
}
