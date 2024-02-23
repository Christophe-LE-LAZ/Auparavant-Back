<?php

namespace App\Repository;

use App\Entity\Memory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Memory>
 *
 * @method Memory|null find($id, $lockMode = null, $lockVersion = null)
 * @method Memory|null findOneBy(array $criteria, array $orderBy = null)
 * @method Memory[]    findAll()
 * @method Memory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MemoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Memory::class);
    }

    /**
     * Fetch the latest three memories by creation date
     *
     * @return array|null
     */
    public function findTheLatestOnes(): ?array
    {
        return $this->createQueryBuilder('memory')
            ->orderBy('memory.createdAt', 'DESC')
            ->setMaxResults(3)
            ->getQuery()
            ->getResult();
    }

    /**
     * Fetch a user's contributions by his id
     *
     * @param [type] $userId
     * @return array|null
     */
    public function findByUserId($userId): ?array
    {
        return $this->createQueryBuilder('memory')
            ->join('memory.user', 'user')
            ->where('user.id = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }

    /**
     * Fetch two random memory pictures for any address.
     *
     * @return array|null
     */
    public function findTwoRandomMemoryPictures(): ?array
    {
        return $this->createQueryBuilder('memory1')
            ->select('memory1.main_picture as picture1', 'memory2.main_picture as picture2')
            ->join('memory1.location', 'location')
            ->join('App\Entity\Memory', 'memory2', 'WITH', 'memory1.location = memory2.location AND memory1.id != memory2.id')
            ->orderBy('RAND()')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère le souvenir le plus ancien par localité
     */
    public function findOldestMemoryByLocation(int $locationId): ?array {
        $sql = "SELECT m.*
                FROM memory m
                WHERE m.location_id = :locationId
                ORDER BY m.picture_date ASC
                LIMIT 1";
        
        $conn = $this->getEntityManager()->getConnection();
        $resultSet = $conn->executeQuery($sql, ['locationId' => $locationId]);
        return $resultSet->fetchAssociative();
    }
    
    /**
     * Récupère le souvenir le plus récent par localité
     */
    public function findMostRecentMemoryByLocation(int $locationId): ?array {
        $sql = "SELECT m.*
                FROM memory m
                WHERE m.location_id = :locationId
                ORDER BY m.picture_date DESC
                LIMIT 1";
        
        $conn = $this->getEntityManager()->getConnection();
        $resultSet = $conn->executeQuery($sql, ['locationId' => $locationId]);
        return $resultSet->fetchAssociative();
    }








    //    /**
    //     * @return Memory[] Returns an array of Memory objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Memory
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
