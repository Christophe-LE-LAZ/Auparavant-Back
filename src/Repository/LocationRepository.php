<?php

namespace App\Repository;

use App\Entity\Location;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Location>
 *
 * @method Location|null find($id, $lockMode = null, $lockVersion = null)
 * @method Location|null findOneBy(array $criteria, array $orderBy = null)
 * @method Location[]    findAll()
 * @method Location[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LocationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Location::class);
    }

    public function findMemoriesWithLocation(Location $location, int $memoryId): array
    {
        return $this->createQueryBuilder('l')
            ->select('l, m')
            ->leftJoin('l.memories', 'm')
            ->where('l = :location')
            ->andWhere('m.id != :memoryId')  // Exclure le souvenir actuel
            ->setParameter('location', $location)
            ->setParameter('memoryId', $memoryId)
            ->getQuery()
            ->getResult();
    }

    public function findByOrderAlphabetical($direction): array
    {
        $sql = "SELECT l.id as locationId, l.street, l.zipcode, l.city, m.* , p.name as placeName, p.type, u.firstname as userFirstName, u.lastname as userLastName
                FROM location l
                LEFT JOIN memory m ON l.id = m.location_id
                LEFT JOIN place p ON l.id = p.location_id
                LEFT JOIN user u ON m.user_id = u.id
                ORDER BY SUBSTRING(l.street, LOCATE(' ', l.street) + 1) $direction";
    
        $conn = $this->getEntityManager()->getConnection();
        $resultSet = $conn->executeQuery($sql);
        return $resultSet->fetchAllAssociative();
    }
    

//    /**
//     * @return Location[] Returns an array of Location objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Location
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
