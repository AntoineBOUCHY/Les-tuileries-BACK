<?php

namespace App\Entity;

use App\Repository\PageContentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PageContentRepository::class)
 */
class PageContent
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * 
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * 
     */
    private $title;

    /**
     * @ORM\Column(type="smallint")
     * 
     */
    private $pageOrder;

    /**
     * @ORM\ManyToOne(targetEntity=Page::class, inversedBy="pageContents")
     * @ORM\JoinColumn(nullable=false)
     */
    private $page;

    /**
     * @ORM\ManyToOne(targetEntity=Language::class, inversedBy="pageContents")
     * @ORM\JoinColumn(nullable=false)
     * 
     */
    private $language;

    /**
     * @ORM\Column(type="text")
     * 
     */
    private $content;

    /**
     * @ORM\ManyToMany(targetEntity=Picture::class, mappedBy="pageContents")
     * 
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

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getPageOrder(): ?int
    {
        return $this->pageOrder;
    }

    public function setPageOrder(int $pageOrder): self
    {
        $this->pageOrder = $pageOrder;

        return $this;
    }

    public function getPage(): ?Page
    {
        return $this->page;
    }

    public function setPage(?Page $page): self
    {
        $this->page = $page;

        return $this;
    }

    public function getLanguage(): ?Language
    {
        return $this->language;
    }

    public function setLanguage(?Language $language): self
    {
        $this->language = $language;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

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
        }

        return $this;
    }

    public function removePicture(Picture $picture): self
    {
        $this->pictures->removeElement($picture);

        return $this;
    }
}
