<?php

namespace App\Service;

use App\Repository\PageRepository;
use App\Repository\PictureRepository;
use App\Repository\LanguageRepository;
use App\Repository\PageContentRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

class AllContents
{
    private $languageRepository;
    private $pageContentRepository;
    private $pageRepository;
    private $pictureRepository;

    public function __construct(LanguageRepository $languageRepository, PageContentRepository $pageContentRepository, PageRepository $pageRepository, PictureRepository $pictureRepository)
    {
        $this->languageRepository = $languageRepository;
        $this->pageContentRepository = $pageContentRepository;
        $this->pageRepository = $pageRepository;
        $this->pictureRepository = $pictureRepository;
    }

    /**
     * Get the id of the name and language of the current page
     *
     * @param string $nameLanguage The language
     * @param string $namePage The name of the current page
     * @return array|null The page and language Id..
     */
    public function getCurrentPageAndLanguageId(string $nameLanguage, string $namePage): array
    {
        // Get the id of the page to display.
        $idCurrentPage = $this->pageRepository->findOneBy(['name' => $namePage])->getId();
        $this->checkContent($idCurrentPage);
        // Get the language of the content to be displayed.
        $currentLanguage = $this->languageRepository->findOneBy(['country' => $nameLanguage]);
        $this->checkContent($currentLanguage);

        // else, get the id of this language.
        $idCurrentLanguage = $currentLanguage->getId();
        
        // Return the page and language Id.
        return [
            'pageId'     => $idCurrentPage,
            'languageId' => $idCurrentLanguage
        ];
    }

    /**
     * Get the content of the current page
     *
     * @param string $nameLanguage The language id
     * @param string $namePage The id of the current page
     * @return array|null All the current page contents.
     */
    public function getCurrentPageContents(int $idCurrentPage, int $idCurrentLanguage): ?array
    {
        // Get all the contents to be displayed on the current page.
        $allContents = $this->pageContentRepository->getAllCurentPageContent($idCurrentPage, $idCurrentLanguage);
        $this->checkContent($allContents);

        return $allContents;
    }

    /**
     * Get the pictures of the current page
     *
     * @param string $nameLanguage The language id
     * @param string $namePage The id of the current page
     * @return array|null All the current page contents.
     */
    public function getCurrentPagePictures(int $idCurrentPage, int $idCurrentLanguage): ?array
    {
        // Get all pictures to be displayed on the current page.
        $allPictures = $this->pictureRepository->getAllPictureCurrentPage($idCurrentPage, $idCurrentLanguage);
        $this->checkContent($allPictures);

        return $allPictures;
    }

    /**
     * Check if the requested content exist
     *
     * @param [type] $contentToCheck The content to check
     * @return JsonResponse|null
     */
    public function checkContent($contentToCheck): ?JsonResponse
    {
        if(null === $contentToCheck) {

            return new JsonResponse(['message' => 'Aucune données trouvées'], 400);
        }

        return null;
    }
}