<?php

namespace App\Entity;

use App\Repository\LanguageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LanguageRepository::class)
 */
class Language
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=10)
     * 
     * 
     */
    private $country;

    /**
     * @ORM\OneToMany(targetEntity=PageContent::class, mappedBy="language", orphanRemoval=true)
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

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

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
            $pageContent->setLanguage($this);
        }

        return $this;
    }

    public function removePageContent(PageContent $pageContent): self
    {
        if ($this->pageContents->removeElement($pageContent)) {
            // set the owning side to null (unless already changed)
            if ($pageContent->getLanguage() === $this) {
                $pageContent->setLanguage(null);
            }
        }

        return $this;
    }
}
