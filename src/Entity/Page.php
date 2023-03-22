<?php

namespace App\Entity;

use App\Repository\PageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PageRepository::class)
 */
class Page
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * 
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     * 
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=PageContent::class, mappedBy="page", orphanRemoval=true)
     */
    private $pageContents;

    public function __construct()
    {
        $this->pageContents = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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
            $pageContent->setPage($this);
        }

        return $this;
    }

    public function removePageContent(PageContent $pageContent): self
    {
        if ($this->pageContents->removeElement($pageContent)) {
            // set the owning side to null (unless already changed)
            if ($pageContent->getPage() === $this) {
                $pageContent->setPage(null);
            }
        }

        return $this;
    }
}
