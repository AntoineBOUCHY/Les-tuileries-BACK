<?php

namespace App\Repository;

use App\Entity\PageContent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PageContent>
 *
 * @method PageContent|null find($id, $lockMode = null, $lockVersion = null)
 * @method PageContent|null findOneBy(array $criteria, array $orderBy = null)
 * @method PageContent[]    findAll()
 * @method PageContent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PageContentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PageContent::class);
    }

    public function add(PageContent $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PageContent $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Get all contents of the current page
     *
     * @param integer $pageId The id of the current page
     * @param integer $languageId The language id
    * @return array Associative array 
     */
    public function getAllCurentPageContent(int $pageId, int $languageId): array
    {
        $conn = $this->getEntityManager()->getConnection();

        // custome request
        $sql = 
        'SELECT `page_content`.`id` AS "page_content_id", `page_content`.`title`, `page_content`.`page_order`, `page_content`.`content`, `picture`.`id` AS "picture_id" , `picture`.`path` AS "picture_path", `picture`.`alt` AS "picture_alt" FROM `page_content` 
        LEFT JOIN `picture_page_content` ON `picture_page_content`.`page_content_id` = `page_content`.`id` 
        LEFT JOIN `picture` ON `picture`.`id` = `picture_page_content`.`picture_id` 
        WHERE `page_id` = :pageId AND `language_id` = :languageId 
        ORDER BY `page_content`.`page_order` ASC';
        
        // Execute the prepared request
        $stmt = $conn->executeQuery($sql, [
            "languageId" => $languageId,
            "pageId" => $pageId
        ]);

        // Return an associative array
        return $stmt->fetchAllAssociative();
    }

    /**
     * Find the content by the id of the current page
     *
     * @param integer $pageId The id of the current page
     * @return array Associative array
     */
    public function findByPageId(int $pageId): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 
        'SELECT `id`, `title`, `page_order`, `content` FROM `page_content`
        WHERE `page_id` = :pageId';
        
        $stmt = $conn->executeQuery($sql, [
            "pageId" => $pageId
        ]);

        return $stmt->fetchAllAssociative();
    }

//    /**
//     * @return PageContent[] Returns an array of PageContent objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?PageContent
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
