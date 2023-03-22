<?php

namespace App\Repository;

use App\Entity\Picture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Picture>
 *
 * @method Picture|null find($id, $lockMode = null, $lockVersion = null)
 * @method Picture|null findOneBy(array $criteria, array $orderBy = null)
 * @method Picture[]    findAll()
 * @method Picture[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PictureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Picture::class);
    }

    public function add(Picture $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Picture $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getAllPictureCurrentPage($pageId, $languageId)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 
        'SELECT  `picture`.`id` AS "picture_id" , `picture`.`path` AS "picture_path", `picture`.`alt` AS "picture_alt" 
        FROM `picture` 
        LEFT JOIN `picture_page_content` ON `picture`.`id` = `picture_page_content`.`picture_id` 
        LEFT JOIN `page_content` ON `picture_page_content`.`page_content_id` = `page_content`.`id` 
        WHERE `page_id` = :pageId AND `language_id` = :languageId';
        
        $stmt = $conn->executeQuery($sql, [
            "languageId" => $languageId,
            "pageId" => $pageId
        ]);
        
        return $stmt->fetchAllAssociative();
    }
//    /**
//     * @return Picture[] Returns an array of Picture objects
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

//    public function findOneBySomeField($value): ?Picture
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
